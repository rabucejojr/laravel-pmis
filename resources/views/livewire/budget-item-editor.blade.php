<div>
    {{-- Budget warning --}}
    @if($budgetWarning)
        <div class="mb-4 flex items-start gap-2 rounded-lg px-4 py-3 text-sm bg-amber-50 border border-amber-200 text-amber-800">
            <svg class="w-4 h-4 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            </svg>
            <span>{{ $budgetWarning }}</span>
        </div>
    @endif

    @if($errors->has('tree'))
        <div class="mb-4 flex items-start gap-2 rounded-lg px-4 py-3 text-sm bg-red-50 border border-red-200 text-red-800">
            <svg class="w-4 h-4 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>{{ $errors->first('tree') }}</span>
        </div>
    @endif

    <div
        x-data="budgetEditor(@js($project->id), @js(\App\Services\BudgetItemService::make()->toJsonTree($project)))"
        @budget-saved.window="isDirty = false"
    >
        {{-- ── Toolbar ── --}}
        <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
            <div class="flex items-center gap-3">
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Budget Breakdown</h3>
                <span x-show="isDirty" x-cloak
                      class="inline-flex items-center gap-1 text-xs font-medium px-2 py-0.5 rounded-full bg-amber-100 text-amber-700">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse inline-block"></span>
                    Unsaved changes
                </span>
            </div>

            @if(auth()->user()->hasRole(['admin', 'encoder']))
            <div class="flex items-center gap-2">
                {{-- Add Group --}}
                <button type="button" @click="addRootGroup()"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border text-xs font-semibold transition-all"
                        style="border-color:#003087;color:#003087;"
                        onmouseover="this.style.background='#003087';this.style.color='#fff'"
                        onmouseout="this.style.background='';this.style.color='#003087'"
                        title="Add a top-level group / category">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 7h18M3 12h18M3 17h18"/>
                    </svg>
                    Add Group
                </button>
                {{-- Add Root Item --}}
                <button type="button" @click="addRootItem()"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border text-xs font-semibold transition-all border-gray-300 text-gray-600"
                        onmouseover="this.style.background='#f3f4f6'"
                        onmouseout="this.style.background=''"
                        title="Add a standalone line item at the top level">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add Item
                </button>
                {{-- Save --}}
                <button type="button" @click="save()"
                        :disabled="!isDirty || saving"
                        class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-lg text-xs font-semibold text-white transition-all disabled:opacity-40 disabled:cursor-not-allowed"
                        style="background-color:#003087;"
                        onmouseover="if(!this.disabled) this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                    <svg x-show="saving" class="animate-spin w-3.5 h-3.5" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                    </svg>
                    <svg x-show="!saving" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                    </svg>
                    <span x-text="saving ? 'Saving…' : 'Save Budget'"></span>
                </button>
            </div>
            @endif
        </div>

        {{-- ── Grand total bar ── --}}
        <div class="rounded-xl border border-gray-200 mb-4 overflow-hidden">
            <div class="flex items-center justify-between px-4 py-3" style="background:linear-gradient(135deg,#003087 0%,#00509e 100%);">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M4 19h16a1 1 0 001-1V6a1 1 0 00-1-1H4a1 1 0 00-1 1v12a1 1 0 001 1z"/>
                    </svg>
                    <span class="text-xs font-semibold text-blue-100 uppercase tracking-wide">Computed Grand Total</span>
                </div>
                <span class="font-mono font-bold text-xl text-white">
                    ₱ <span x-text="formatMoney(grandTotal())"></span>
                </span>
            </div>
            <div class="flex items-center justify-between px-4 py-2 bg-gray-50 border-t border-gray-200 text-xs text-gray-500">
                <span>Last saved (server-verified)</span>
                <span class="font-mono font-semibold" style="color:#003087;">₱ {{ number_format($grandTotal, 2) }}</span>
            </div>
        </div>

        {{-- ── Tree rows ── --}}
        <div class="space-y-1.5">
            <template x-for="(item, index) in flatItems" :key="item._id">
                <div x-show="isVisible(item)" x-cloak>
                    <div class="rounded-lg border overflow-hidden transition-shadow hover:shadow-sm"
                         :class="item.item_type === 'group' ? 'border-blue-100' : 'border-gray-200'"
                         :style="'margin-left:' + (item.depth * 24) + 'px'">

                        {{-- Depth accent bar --}}
                        <div class="flex">
                            <div class="w-1 shrink-0 rounded-l-lg"
                                 :style="item.item_type === 'group'
                                     ? 'background-color:#003087'
                                     : (item.depth === 0 ? 'background-color:#6b7280' : 'background-color:#d1d5db')">
                            </div>

                            <div class="flex-1 min-w-0"
                                 :class="item.item_type === 'group' ? 'bg-blue-50' : 'bg-white'">

                                {{-- Main row --}}
                                <div class="flex items-center gap-2 px-3 py-2.5">

                                    {{-- Expand/collapse for groups --}}
                                    <button type="button"
                                            x-show="item.item_type === 'group'"
                                            @click="flatItems[index]._open = !flatItems[index]._open"
                                            class="shrink-0 w-5 h-5 flex items-center justify-center rounded text-gray-400 hover:text-gray-700 hover:bg-white transition-colors">
                                        <svg class="w-3.5 h-3.5 transition-transform duration-200"
                                             :class="item._open ? 'rotate-90' : ''"
                                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </button>

                                    {{-- Expand/collapse for item detail --}}
                                    <button type="button"
                                            x-show="item.item_type === 'item'"
                                            @click="flatItems[index]._detailOpen = !flatItems[index]._detailOpen"
                                            :title="item._detailOpen ? 'Collapse details' : 'Expand details'"
                                            class="shrink-0 w-5 h-5 flex items-center justify-center rounded text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition-colors">
                                        <svg class="w-3.5 h-3.5 transition-transform duration-200"
                                             :class="item._detailOpen ? 'rotate-180' : ''"
                                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </button>

                                    {{-- Type badge --}}
                                    <span class="shrink-0 text-xs font-bold px-1.5 py-0.5 rounded-md leading-none select-none"
                                          :class="item.item_type === 'group'
                                              ? 'text-white'
                                              : 'bg-gray-100 text-gray-500'"
                                          :style="item.item_type === 'group' ? 'background-color:#003087' : ''">
                                        <span x-text="item.item_type === 'group' ? 'GRP' : 'ITEM'"></span>
                                    </span>

                                    {{-- Label --}}
                                    @if(auth()->user()->hasRole(['admin', 'encoder']))
                                    <input x-model="flatItems[index].label" type="text"
                                           @input="isDirty = true"
                                           :placeholder="item.item_type === 'group' ? 'Group / category label…' : 'Line item description…'"
                                           class="flex-1 min-w-0 rounded-md border px-2.5 py-1.5 text-sm transition-colors focus:outline-none focus:ring-2 focus:border-transparent"
                                           :class="item.item_type === 'group'
                                               ? 'font-semibold border-blue-200 bg-transparent focus:ring-blue-300'
                                               : 'border-gray-200 bg-white focus:ring-blue-300'"
                                           style="--tw-ring-color: #003087;">
                                    @else
                                    <span class="flex-1 min-w-0 text-sm truncate"
                                          :class="item.item_type === 'group' ? 'font-semibold text-gray-800' : 'text-gray-700'"
                                          x-text="item.label || '—'"></span>
                                    @endif

                                    {{-- Computed amount --}}
                                    <div class="shrink-0 text-right min-w-36">
                                        <span class="font-mono text-sm font-bold" style="color:#003087;">
                                            ₱ <span x-text="formatMoney(computeAmount(item))"></span>
                                        </span>
                                        <p class="text-xs text-gray-400 leading-none mt-0.5"
                                           x-show="item.item_type === 'item' && item.quantity && item.unit_cost">
                                            <span x-text="item.quantity"></span>
                                            <span x-show="item.unit_label" x-text="item.unit_label"></span>
                                            × ₱<span x-text="formatMoney(item.unit_cost || 0)"></span>
                                        </p>
                                    </div>

                                    {{-- Action buttons --}}
                                    @if(auth()->user()->hasRole(['admin', 'encoder']))
                                    <div class="flex items-center gap-0.5 shrink-0 ml-1">
                                        {{-- Move up --}}
                                        <button type="button" @click="moveUp(index)"
                                                :disabled="!canMoveUp(index)"
                                                title="Move up"
                                                class="p-1.5 rounded text-gray-300 hover:text-gray-600 hover:bg-gray-100 disabled:opacity-20 disabled:cursor-not-allowed transition-colors">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7"/>
                                            </svg>
                                        </button>
                                        {{-- Move down --}}
                                        <button type="button" @click="moveDown(index)"
                                                :disabled="!canMoveDown(index)"
                                                title="Move down"
                                                class="p-1.5 rounded text-gray-300 hover:text-gray-600 hover:bg-gray-100 disabled:opacity-20 disabled:cursor-not-allowed transition-colors">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        </button>

                                        {{-- Group-only: add sub-group / add item --}}
                                        <template x-if="item.item_type === 'group'">
                                            <div class="flex items-center gap-0.5 ml-1 pl-1.5 border-l border-gray-200">
                                                <button type="button" @click="addChildGroup(item)"
                                                        title="Add sub-group inside this group"
                                                        class="inline-flex items-center gap-1 px-1.5 py-1 rounded text-xs font-semibold text-gray-400 hover:text-blue-700 hover:bg-blue-50 transition-colors">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                                                    </svg>
                                                    GRP
                                                </button>
                                                <button type="button" @click="addChildItem(item)"
                                                        title="Add line item inside this group"
                                                        class="inline-flex items-center gap-1 px-1.5 py-1 rounded text-xs font-semibold text-gray-400 hover:text-green-700 hover:bg-green-50 transition-colors">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                                                    </svg>
                                                    ITEM
                                                </button>
                                            </div>
                                        </template>

                                        {{-- Delete --}}
                                        <button type="button" @click="removeItem(index)"
                                                title="Remove this item"
                                                class="p-1.5 rounded ml-1 text-red-300 hover:text-red-600 hover:bg-red-50 transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>
                                    @endif
                                </div>

                                {{-- ── Item detail fields (collapsible) ── --}}
                                <div x-show="item.item_type === 'item' && item._detailOpen"
                                     x-collapse
                                     class="border-t border-gray-100 bg-gray-50">
                                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 px-4 py-3">

                                        <div>
                                            <label class="block text-xs font-semibold text-gray-500 mb-1">Quantity</label>
                                            @if(auth()->user()->hasRole(['admin', 'encoder']))
                                            <input x-model.number="flatItems[index].quantity" type="number" min="0" step="0.0001"
                                                   @input="isDirty = true"
                                                   placeholder="e.g. 12"
                                                   class="w-full rounded-md border border-gray-200 bg-white px-2.5 py-1.5 text-xs text-right font-mono focus:outline-none focus:ring-2 focus:border-transparent"
                                                   style="--tw-ring-color:#003087;">
                                            @else
                                            <p class="font-mono text-xs text-right py-1.5" x-text="item.quantity ?? '—'"></p>
                                            @endif
                                        </div>

                                        <div>
                                            <label class="block text-xs font-semibold text-gray-500 mb-1">Unit</label>
                                            @if(auth()->user()->hasRole(['admin', 'encoder']))
                                            <input x-model="flatItems[index].unit_label" type="text"
                                                   @input="isDirty = true"
                                                   placeholder="lot, pax, month…"
                                                   class="w-full rounded-md border border-gray-200 bg-white px-2.5 py-1.5 text-xs focus:outline-none focus:ring-2 focus:border-transparent"
                                                   style="--tw-ring-color:#003087;">
                                            @else
                                            <p class="text-xs py-1.5" x-text="item.unit_label || '—'"></p>
                                            @endif
                                        </div>

                                        <div>
                                            <label class="block text-xs font-semibold text-gray-500 mb-1">Unit Cost (₱)</label>
                                            @if(auth()->user()->hasRole(['admin', 'encoder']))
                                            <input x-model.number="flatItems[index].unit_cost" type="number" min="0" step="0.01"
                                                   @input="isDirty = true"
                                                   placeholder="0.00"
                                                   class="w-full rounded-md border border-gray-200 bg-white px-2.5 py-1.5 text-xs text-right font-mono focus:outline-none focus:ring-2 focus:border-transparent"
                                                   style="--tw-ring-color:#003087;">
                                            @else
                                            <p class="font-mono text-xs text-right py-1.5" x-text="item.unit_cost ? formatMoney(item.unit_cost) : '—'"></p>
                                            @endif
                                        </div>

                                        <div>
                                            <label class="block text-xs font-semibold text-gray-500 mb-1">
                                                Lump-sum (₱)
                                                <span class="text-gray-400 font-normal">if no qty/cost</span>
                                            </label>
                                            @if(auth()->user()->hasRole(['admin', 'encoder']))
                                            <input x-model.number="flatItems[index].amount" type="number" min="0" step="0.01"
                                                   @input="isDirty = true"
                                                   placeholder="0.00"
                                                   class="w-full rounded-md border border-gray-200 bg-white px-2.5 py-1.5 text-xs text-right font-mono focus:outline-none focus:ring-2 focus:border-transparent"
                                                   style="--tw-ring-color:#003087;">
                                            @else
                                            <p class="font-mono text-xs text-right py-1.5" x-text="item.amount ? formatMoney(item.amount) : '—'"></p>
                                            @endif
                                        </div>

                                        <div class="col-span-2 sm:col-span-4">
                                            <label class="block text-xs font-semibold text-gray-500 mb-1">Remarks</label>
                                            @if(auth()->user()->hasRole(['admin', 'encoder']))
                                            <input x-model="flatItems[index].remarks" type="text"
                                                   @input="isDirty = true"
                                                   placeholder="Remarks and/or Justification…"
                                                   class="w-full rounded-md border border-gray-200 bg-white px-2.5 py-1.5 text-xs focus:outline-none focus:ring-2 focus:border-transparent"
                                                   style="--tw-ring-color:#003087;">
                                            @else
                                            <p class="text-xs text-gray-500 py-1.5" x-text="item.remarks || '—'"></p>
                                            @endif
                                        </div>

                                    </div>

                                    {{-- Live amount preview --}}
                                    <div class="flex items-center justify-end gap-2 px-4 pb-2.5 text-xs text-gray-400">
                                        <span>Computed:</span>
                                        <span class="font-mono font-semibold" style="color:#003087;">
                                            ₱ <span x-text="formatMoney(computeAmount(item))"></span>
                                        </span>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        {{-- ── Empty state ── --}}
        <div x-show="flatItems.length === 0" x-cloak
             class="flex flex-col items-center justify-center py-14 text-center border-2 border-dashed border-gray-200 rounded-xl mt-2">
            <svg class="w-10 h-10 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M4 19h16a1 1 0 001-1V6a1 1 0 00-1-1H4a1 1 0 00-1 1v12a1 1 0 001 1z"/>
            </svg>
            <p class="text-sm font-medium text-gray-500">No budget items yet</p>
            @if(auth()->user()->hasRole(['admin', 'encoder']))
                <p class="text-xs text-gray-400 mt-1">
                    Click <strong class="text-gray-600">Add Group</strong> to create a category,
                    or <strong class="text-gray-600">Add Item</strong> for a standalone line item.
                </p>
            @else
                <p class="text-xs text-gray-400 mt-1">No budget breakdown has been entered for this project yet.</p>
            @endif
        </div>

    </div>

    @push('scripts')
    <script>
    function budgetEditor(projectId, initialTree) {
        return {
            projectId : projectId,
            flatItems : [],
            isDirty   : false,
            saving    : false,
            _uid      : 0,

            init() {
                this.flatItems = this.flattenTree(initialTree, null, 0);
                window.addEventListener('beforeunload', (e) => {
                    if (this.isDirty && !this.saving) { e.preventDefault(); e.returnValue = ''; }
                });
            },

            flattenTree(nodes, parentId, depth) {
                const flat = [];
                for (const n of (nodes || [])) {
                    const id = String(++this._uid);
                    flat.push({
                        _id         : id,
                        db_id       : n.db_id      ?? null,
                        _parent_id  : parentId,
                        depth       : depth,
                        item_type   : n.item_type,
                        label       : n.label      ?? '',
                        quantity    : n.quantity   !== undefined ? n.quantity   : null,
                        unit_cost   : n.unit_cost  !== undefined ? n.unit_cost  : null,
                        unit_label  : n.unit_label ?? '',
                        amount      : n.amount     !== undefined ? n.amount     : null,
                        remarks     : n.remarks    ?? '',
                        _open       : true,
                        _detailOpen : true,
                    });
                    if ((n.children || []).length) {
                        flat.push(...this.flattenTree(n.children, id, depth + 1));
                    }
                }
                return flat;
            },

            nestFlat() {
                const map = {};
                for (const item of this.flatItems) {
                    map[item._id] = {
                        db_id      : item.db_id,
                        item_type  : item.item_type,
                        label      : item.label,
                        quantity   : item.quantity  !== '' ? item.quantity  : null,
                        unit_cost  : item.unit_cost !== '' ? item.unit_cost : null,
                        unit_label : item.unit_label || null,
                        amount     : item.amount    !== '' ? item.amount    : null,
                        remarks    : item.remarks   || null,
                        children   : [],
                    };
                }
                const roots = [];
                for (const item of this.flatItems) {
                    if (item._parent_id && map[item._parent_id]) {
                        map[item._parent_id].children.push(map[item._id]);
                    } else {
                        roots.push(map[item._id]);
                    }
                }
                return roots;
            },

            isVisible(item) {
                if (!item._parent_id) return true;
                const parent = this.flatItems.find(i => i._id === item._parent_id);
                if (!parent) return true;
                return parent._open && this.isVisible(parent);
            },

            makeNode(type, parentId, depth) {
                return {
                    _id         : String(++this._uid),
                    db_id       : null,
                    _parent_id  : parentId,
                    depth       : depth,
                    item_type   : type,
                    label       : '',
                    quantity    : null,
                    unit_cost   : null,
                    unit_label  : '',
                    amount      : null,
                    remarks     : '',
                    _open       : true,
                    _detailOpen : true,
                };
            },

            addRootGroup() {
                this.flatItems.push(this.makeNode('group', null, 0));
                this.isDirty = true;
            },

            addRootItem() {
                this.flatItems.push(this.makeNode('item', null, 0));
                this.isDirty = true;
            },

            addChildGroup(parent) {
                const insertAt = this.lastDescendantIndex(parent._id) + 1;
                this.flatItems.splice(insertAt, 0, this.makeNode('group', parent._id, parent.depth + 1));
                const idx = this.flatItems.findIndex(i => i._id === parent._id);
                if (idx >= 0) this.flatItems[idx]._open = true;
                this.isDirty = true;
            },

            addChildItem(parent) {
                const insertAt = this.lastDescendantIndex(parent._id) + 1;
                this.flatItems.splice(insertAt, 0, this.makeNode('item', parent._id, parent.depth + 1));
                const idx = this.flatItems.findIndex(i => i._id === parent._id);
                if (idx >= 0) this.flatItems[idx]._open = true;
                this.isDirty = true;
            },

            removeItem(index) {
                const item = this.flatItems[index];
                const toRemove = new Set([item._id]);
                for (let i = index + 1; i < this.flatItems.length; i++) {
                    if (toRemove.has(this.flatItems[i]._parent_id)) {
                        toRemove.add(this.flatItems[i]._id);
                    }
                }
                this.flatItems = this.flatItems.filter(i => !toRemove.has(i._id));
                this.isDirty = true;
            },

            moveUp(index) {
                const prevSibIdx = this.prevSiblingIndex(index);
                if (prevSibIdx < 0) return;
                const block     = this.getBlock(index);
                const prevBlock = this.getBlock(prevSibIdx);
                const before = this.flatItems.slice(0, prevSibIdx);
                const after  = this.flatItems.slice(prevSibIdx + prevBlock.length + block.length);
                this.flatItems = [...before, ...block, ...prevBlock, ...after];
                this.isDirty = true;
            },

            moveDown(index) {
                const nextSibIdx = this.nextSiblingIndex(index);
                if (nextSibIdx < 0) return;
                const block     = this.getBlock(index);
                const nextBlock = this.getBlock(nextSibIdx);
                const before = this.flatItems.slice(0, index);
                const after  = this.flatItems.slice(index + block.length + nextBlock.length);
                this.flatItems = [...before, ...nextBlock, ...block, ...after];
                this.isDirty = true;
            },

            canMoveUp(index) { return this.prevSiblingIndex(index) >= 0; },
            canMoveDown(index) { return this.nextSiblingIndex(index) >= 0; },

            lastDescendantIndex(id) {
                let last = this.flatItems.findIndex(i => i._id === id);
                const descendants = new Set([id]);
                for (let i = last + 1; i < this.flatItems.length; i++) {
                    if (descendants.has(this.flatItems[i]._parent_id)) {
                        descendants.add(this.flatItems[i]._id);
                        last = i;
                    }
                }
                return last;
            },

            getBlock(startIndex) {
                const root  = this.flatItems[startIndex];
                const block = [root];
                const ids   = new Set([root._id]);
                for (let i = startIndex + 1; i < this.flatItems.length; i++) {
                    if (ids.has(this.flatItems[i]._parent_id)) {
                        ids.add(this.flatItems[i]._id);
                        block.push(this.flatItems[i]);
                    } else {
                        break;
                    }
                }
                return block;
            },

            prevSiblingIndex(index) {
                const item = this.flatItems[index];
                for (let i = index - 1; i >= 0; i--) {
                    const c = this.flatItems[i];
                    if (c.depth < item.depth) return -1;
                    if (c.depth === item.depth && c._parent_id === item._parent_id) return i;
                }
                return -1;
            },

            nextSiblingIndex(index) {
                const item    = this.flatItems[index];
                const block   = this.getBlock(index);
                const nextIdx = index + block.length;
                if (nextIdx >= this.flatItems.length) return -1;
                const next = this.flatItems[nextIdx];
                if (next.depth === item.depth && next._parent_id === item._parent_id) return nextIdx;
                return -1;
            },

            computeAmount(item) {
                if (item.item_type === 'group') {
                    return this.flatItems
                        .filter(i => i._parent_id === item._id)
                        .reduce((sum, child) => sum + this.computeAmount(child), 0);
                }
                const qty  = parseFloat(item.quantity);
                const cost = parseFloat(item.unit_cost);
                if (!isNaN(qty) && !isNaN(cost)) return qty * cost;
                return parseFloat(item.amount ?? 0) || 0;
            },

            grandTotal() {
                return this.flatItems
                    .filter(i => i._parent_id === null)
                    .reduce((s, i) => s + this.computeAmount(i), 0);
            },

            formatMoney(v) {
                return Number(v).toLocaleString('en-PH', {
                    minimumFractionDigits : 2,
                    maximumFractionDigits : 2,
                });
            },

            save() {
                if (!this.isDirty || this.saving) return;
                this.saving = true;
                this.$wire.saveTree(this.nestFlat())
                     .then(() => { this.saving = false; })
                     .catch(() => { this.saving = false; });
            },
        };
    }
    </script>
    @endpush
</div>
