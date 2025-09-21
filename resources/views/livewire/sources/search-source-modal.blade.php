<!-- Large Modal -->
<div wire:ignore.self class="modal fade" id="searchModal" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-content-demo">
            <div class="modal-header">
                <h6 class="modal-title">Source search</h6><button aria-label="Close" class="btn-close"
                    data-bs-dismiss="modal" type="button"><span aria-hidden="true">×</span></button>
            </div>
            <div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="start_date">Start Date</label>
                        <input type="date" class="form-control" id="start_date" wire:model.defer="filters.start_date">
                        @error('start_date') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="end_date">End Date</label>
                        <input type="date" class="form-control" id="end_date" wire:model.defer="filters.end_date">
                        @error('end_date') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="name">Search</label>
                        <input type="text" class="form-control" id="name" placeholder="Enter Name"
                            wire:model.defer="filters.name">
                        @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn ripple btn-primary" wire:click="search" type="button">Search</button>
                    <button class="btn ripple btn-primary" wire:click="resetfields" type="button">reset</button>
                    <button class="btn ripple btn-secondary" data-bs-dismiss="modal" type="button">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!--End Large Modal -->
