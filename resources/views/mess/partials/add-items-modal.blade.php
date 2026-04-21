<div class="modal fade" id="addItemModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="ti ti-shopping-cart me-2"></i>Add Items</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ $addRoute }}" method="POST" id="addItemsForm">
                @csrf
                <div class="modal-body">

                    {{-- Input row --}}
                    <div class="border rounded p-3 mb-3 bg-light">
                        <div class="row g-2 align-items-end">
                            <div class="col-12 col-sm-6 col-md-4">
                                <label class="form-label fw-semibold small">Item Name <span class="text-danger">*</span></label>
                                <input type="text" id="ai_name" class="form-control form-control-sm" placeholder="e.g. Fish, Oil, Rice">
                            </div>
                            <div class="col-6 col-sm-3 col-md-2">
                                <label class="form-label small">Qty</label>
                                <input type="text" id="ai_qty" class="form-control form-control-sm" placeholder="2">
                            </div>
                            <div class="col-6 col-sm-3 col-md-2">
                                <label class="form-label small">Unit</label>
                                <input type="text" id="ai_unit" class="form-control form-control-sm" placeholder="kg/pcs">
                            </div>
                            <div class="col-6 col-sm-3 col-md-2">
                                <label class="form-label small">Cost (৳)</label>
                                <input type="number" id="ai_actual" class="form-control form-control-sm" step="0.01" min="0" placeholder="0">
                            </div>
                            <div class="col-12 col-sm-6 col-md-4">
                                <label class="form-label small">Bought By</label>
                                <input type="text" class="form-control form-control-sm bg-light"
                                    value="{{ $routine->assignedTo->name ?? '—' }}" readonly>
                                <input type="hidden" id="ai_buyer" value="{{ $routine->assigned_to }}">
                            </div>
                            <div class="col-12 col-sm-6 col-md-4">
                                <label class="form-label small">Purchase Date</label>
                                <input type="text" class="form-control form-control-sm bg-light"
                                    value="{{ $routine->start_date?->format('d M Y') ?? now()->format('d M Y') }}" readonly>
                                <input type="hidden" id="ai_date" value="{{ $routine->start_date?->format('Y-m-d') ?? now()->format('Y-m-d') }}">
                            </div>
                            <div class="col-12 col-md-4 d-flex align-items-end">
                                <button type="button" class="btn btn-success w-100 btn-sm" onclick="addItemRow()">
                                    <i class="ti ti-plus me-1"></i>Add to List
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Preview list --}}
                    <div id="aiPreviewWrap" class="d-none">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-semibold small text-muted">Items to save (<span id="aiCount">0</span>)</span>
                            <span class="small fw-bold text-success">Total: ৳<span id="aiTotal">0</span></span>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered mb-0 align-middle" id="aiPreviewTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>Item</th>
                                        <th class="text-center">Qty</th>
                                        <th class="text-end">Cost (৳)</th>
                                        <th class="text-center">Buyer</th>
                                        <th class="text-center">Date</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="aiPreviewBody"></tbody>
                            </table>
                        </div>
                    </div>

                    <div id="aiEmpty" class="text-center text-muted py-3 small">
                        <i class="ti ti-list-check fs-4 d-block mb-1 opacity-40"></i>
                        No items added yet. Fill the form above and click <strong>Add to List</strong>.
                    </div>

                    {{-- Hidden inputs built by JS --}}
                    <div id="aiHiddenInputs"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="aiSubmitBtn" disabled>
                        <i class="ti ti-device-floppy me-1"></i>Save <span id="aiSubmitCount">0</span> Item(s)
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
(function(){
    var aiItems = [];
    var memberNames = {
        '': '{{ addslashes($routine->assignedTo->name ?? 'Default') }}'
        @foreach($members as $m)
        , '{{ $m->user->id }}': '{{ addslashes($m->user->name) }}'
        @endforeach
    };

    window.addItemRow = function() {
        var name = document.getElementById('ai_name').value.trim();
        if (!name) {
            document.getElementById('ai_name').focus();
            document.getElementById('ai_name').classList.add('is-invalid');
            return;
        }
        document.getElementById('ai_name').classList.remove('is-invalid');

        var item = {
            item_name:      name,
            quantity:       document.getElementById('ai_qty').value.trim(),
            unit:           document.getElementById('ai_unit').value.trim(),
            actual_cost:    parseFloat(document.getElementById('ai_actual').value) || 0,
            assigned_to:    document.getElementById('ai_buyer').value,
            expense_date:   document.getElementById('ai_date').value,
        };
        aiItems.push(item);

        // Clear inputs for next item
        document.getElementById('ai_name').value   = '';
        document.getElementById('ai_qty').value    = '';
        document.getElementById('ai_unit').value   = '';

        document.getElementById('ai_actual').value = '';
        document.getElementById('ai_name').focus();

        renderPreview();
    };

    function renderPreview() {
        var body  = document.getElementById('aiPreviewBody');
        var wrap  = document.getElementById('aiPreviewWrap');
        var empty = document.getElementById('aiEmpty');
        var count = document.getElementById('aiCount');
        var total = document.getElementById('aiTotal');
        var btn   = document.getElementById('aiSubmitBtn');
        var submitCount = document.getElementById('aiSubmitCount');
        var hidden = document.getElementById('aiHiddenInputs');

        body.innerHTML  = '';
        hidden.innerHTML = '';

        var grandTotal = 0;

        aiItems.forEach(function(item, i) {
            grandTotal += item.actual_cost;

            // Preview row
            var tr = document.createElement('tr');
            tr.innerHTML =
                '<td class="fw-semibold">' + escHtml(item.item_name) + '</td>' +
                '<td class="text-center text-muted small">' + (item.quantity ? escHtml(item.quantity) + (item.unit ? ' ' + escHtml(item.unit) : '') : '—') + '</td>' +
                '<td class="text-end small fw-semibold text-success">' + (item.actual_cost > 0 ? '৳' + item.actual_cost.toFixed(0) : '—') + '</td>' +
                '<td class="text-center small">' + escHtml(memberNames[item.assigned_to] || memberNames['']) + '</td>' +
                '<td class="text-center small">' + (item.expense_date || '—') + '</td>' +
                '<td class="text-center">' +
                    '<button type="button" class="btn btn-xs btn-outline-danger py-0" onclick="removeAiItem(' + i + ')" title="Remove">' +
                        '<i class="ti ti-x" style="font-size:10px"></i>' +
                    '</button>' +
                '</td>';
            body.appendChild(tr);

            // Hidden inputs
            var fields = ['item_name','quantity','unit','actual_cost','assigned_to','expense_date'];
            fields.forEach(function(f) {
                var inp = document.createElement('input');
                inp.type  = 'hidden';
                inp.name  = 'items[' + i + '][' + f + ']';
                inp.value = item[f] || '';
                hidden.appendChild(inp);
            });
        });

        var hasItems = aiItems.length > 0;
        wrap.classList.toggle('d-none', !hasItems);
        empty.classList.toggle('d-none', hasItems);
        count.textContent = aiItems.length;
        submitCount.textContent = aiItems.length;
        total.textContent = grandTotal.toFixed(0);
        btn.disabled = !hasItems;
    }

    window.removeAiItem = function(i) {
        aiItems.splice(i, 1);
        renderPreview();
    };

    // Reset on modal close
    document.getElementById('addItemModal').addEventListener('hidden.bs.modal', function() {
        aiItems = [];
        renderPreview();
        document.getElementById('ai_name').value   = '';
        document.getElementById('ai_qty').value    = '';
        document.getElementById('ai_unit').value   = '';

        document.getElementById('ai_actual').value = '';
    });

    // Allow pressing Enter in item name to add
    document.getElementById('ai_name').addEventListener('keydown', function(e) {
        if (e.key === 'Enter') { e.preventDefault(); addItemRow(); }
    });

    function escHtml(str) {
        return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }
})();
</script>
