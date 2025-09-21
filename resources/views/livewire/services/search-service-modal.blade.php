<!-- Large Modal -->
<div wire:ignore.self class="modal fade" id="searchModal" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-content-demo">
            <div class="modal-header">
                <h6 class="modal-title">Service Search</h6>
                <button aria-label="Close" class="btn-close" data-bs-dismiss="modal" type="button">
                    <span aria-hidden="true">×</span>
                </button>
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
                    <div class="form-group">
                        <label for="min_price">Min Price</label>
                        <input type="number" class="form-control" id="min_price" placeholder="Enter Min Price"
                            wire:model.defer="filters.min_price">
                        @error('min_price') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="max_price">Max Price</label>
                        <input type="number" class="form-control" id="max_price" placeholder="Enter Max Price"
                            wire:model.defer="filters.max_price">
                        @error('max_price') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn ripple btn-primary" wire:click="search" type="button">Search</button>
                    <button class="btn ripple btn-primary" wire:click="resetfields" type="button">Reset</button>
                    <button class="btn ripple btn-secondary" data-bs-dismiss="modal" type="button">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Large Modal -->
