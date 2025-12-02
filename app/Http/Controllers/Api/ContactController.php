<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\GeneralException;
use Exception;
use Illuminate\Http\Request;
use App\DTO\Contact\ContactDTO;
use App\Enums\ContactMethods;
use App\Exports\ContactsExport;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\Contacts\ContactRequest;
use App\Http\Resources\ContactResource;
use App\Http\Resources\ContactShowResource;
use App\Imports\ContactsImport;
use App\Services\ContactService;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class ContactController extends Controller
{
    public function __construct(public ContactService $contactService)
    {
        $this->middleware('permission:view-contacts')->only(['index', 'show']);
        $this->middleware('permission:create-contacts')->only(['store']);
        $this->middleware('permission:edit-contacts')->only(['update']);
        $this->middleware('permission:delete-contacts')->only(['destroy']);
    }

    public function get_statistics()
    {
        try {
            $statistics = $this->contactService->get_statistics();
            return ApiResponse($statistics, 'Statistics retrieved successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    public function index(Request $request)
    {
        try {
            $filters = array_filter($request->all(), function ($value) {
                return ($value !== null && $value !== false && $value !== '');
            });
            $withRelations = ['user', 'source', 'contactPhones'];
            if ($request->has('ddl')) {
                $contacts = $this->contactService->index($filters, $withRelations);
                $data = ContactResource::collection($contacts);
            } else {
                $contacts = $this->contactService->index($filters, $withRelations, $filters['per_page'] ?? 10);
                $data = ContactResource::collection($contacts)->response()->getData(true);
            }
            return apiResponse($data, 'Contacts retrieved successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    public function store(ContactRequest $request)
    {
        try {
            DB::beginTransaction();
            $contactDTO = ContactDTO::fromRequest($request);
            $contact = $this->contactService->store($contactDTO);
            DB::commit();
            return ApiResponse(new ContactShowResource($contact), 'Contact created successfully', code: 201);
        } catch (GeneralException $e) {
            return ApiResponse(message: $e->getMessage(), code: $e->getCode());
        } catch (Exception $e) {
            DB::rollBack();
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    public function importPreview(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240'
        ]);

        try {
            $file = $request->file('file');

            // Store file temporarily
            $filePath = $file->store('temp-imports', 'local');

            // Get headers from the Excel file
            $headings = (new HeadingRowImport())->toArray($file);
            $excelHeaders = $headings[0][0] ?? [];

            // Get available database fields
            $databaseFields = $this->contactService->getDatabaseFields();

            return response()->json([
                'success' => true,
                'file_path' => $filePath,
                'excel_headers' => $excelHeaders,
                'database_fields' => $databaseFields,
                'suggested_mapping' => $this->contactService->suggestMapping($excelHeaders, $databaseFields),
                'preview_data' => $this->contactService->getPreviewData($file, 5) // Show first 5 rows
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to read file: ' . $e->getMessage()
            ], 500);
        }
    }

    public function import(Request $request)
    {
        $request->validate([
            'file_path' => 'required|string',
            'column_mapping' => 'required|array',
            'import_settings' => 'nullable|array',
            'import_settings.defaults' => 'nullable|array',
            'import_settings.skip_duplicates' => 'nullable|boolean',
            'import_settings.update_existing' => 'nullable|boolean'
        ]);
        try {

            $filePath = $request->input('file_path');
            $columnMapping = $request->input('column_mapping');
            $importSettings = $request->input('import_settings', []);

            // Validate mapping
            $requiredFields = ['first_name', 'email'];
            foreach ($requiredFields as $field) {
                if (empty($columnMapping[$field])) {
                    return ApiResponse(message: "The field '{$field}' is required and must be mapped.", code: 422);
                }
            }

            // Get file from storage
            $file = storage_path('app/' . $filePath);
            if (!file_exists($file)) {
                return ApiResponse(message: 'Import file not found. Please upload the file again.', code: 404);
            }

            // Perform import
            $import = new ContactsImport($columnMapping, $importSettings);
            Excel::import($import, $file);

            // Clean up temporary file
            \Storage::disk('local')->delete($filePath);

            $failures = $import->failures();

            if ($failures->isNotEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Some rows failed to import',
                    'failures' => $failures->map(function ($failure) {
                        return [
                            'row' => $failure->row(),
                            'attribute' => $failure->attribute(),
                            'errors' => $failure->errors(),
                            'values' => $failure->values(),
                        ];
                    }),
                    'total_failures' => $failures->count()
                ], 422);
            }

            return ApiResponse(message: 'Contacts imported successfully', code: 200);
        } catch (\Exception $e) {
            return ApiResponse(message: 'Import failed: ' . $e->getMessage(), code: 500);
        }
    }

    public function export(Request $request)
    {
        $request->validate([
            'columns' => 'required|array|min:1', // e.g., ['first_name', 'email']
            'columns.*' => 'string'
        ]);
        $columns = $request->input('columns');

        return Excel::download(new ContactsExport($columns), 'contacts.xlsx');
    }

    public function getColumns()
    {
        $columns = $this->contactService->getDatabaseFields();
        return ApiResponse($columns, 'Columns retrieved successfully');
    }

    public function getContactMethods()
    {
        $contactMethods = ContactMethods::options();
        return ApiResponse($contactMethods, 'Contact methods retrieved successfully');
    }

    public function update(ContactRequest $request, int $contact_id)
    {
        try {
            DB::beginTransaction();
            $contactDTO = ContactDTO::fromRequest($request);
            $contact = $this->contactService->update($contact_id, $contactDTO);
            DB::commit();
            return ApiResponse(new ContactResource($contact), 'Contact updated successfully');
        } catch (ValidationException $e) {
            return ApiResponse(message: $e->validator->errors(), code: 422);
        } catch (Exception $e) {
            DB::rollBack();
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    public function show(int $contact)
    {
        try {
            $withRelations = ['country', 'city', 'user', 'source', 'contactPhones','department'];
            $contact = $this->contactService->show($contact, $withRelations);

            return ApiResponse(new ContactShowResource($contact), 'Contact retrieved successfully');
        } catch (ModelNotFoundException $e) {
            return ApiResponse(message: 'Contact not found', code: 404);
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    public function details(int $contact)
    {
        try {
            $withRelations = ['user', 'source', 'contactPhones'];
            $contact = $this->contactService->show($contact, $withRelations);

            return ApiResponse(new ContactResource($contact), 'Contact retrieved successfully');
        } catch (ModelNotFoundException $e) {
            return ApiResponse(message: 'Contact not found', code: 404);
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    public function destroy(int $contact)
    {
        try {
            $this->contactService->delete($contact);
            return ApiResponse(message: 'Contact deleted successfully');
        } catch (ModelNotFoundException $e) {
            return ApiResponse(message: 'Contact not found', code: 404);
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }
}
