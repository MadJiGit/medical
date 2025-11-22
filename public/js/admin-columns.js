/**
 * Column Visibility Toggle for EasyAdmin 4
 * Adds a dropdown to show/hide table columns with localStorage persistence
 */
(function() {
    'use strict';

    const STORAGE_KEY = 'easyadmin_visible_columns';

    // Wait for DOM to be ready
    document.addEventListener('DOMContentLoaded', function() {
        // EasyAdmin 4 uses table.datagrid directly
        const table = document.querySelector('table.datagrid');
        if (!table) return;

        // Get current page identifier for storage
        const pageId = getPageIdentifier();

        // Get all column headers
        const headers = table.querySelectorAll('thead th');
        if (headers.length === 0) return;

        // Build column data
        const columns = [];
        headers.forEach((th, index) => {
            const label = getColumnLabel(th, index);
            if (label) {
                columns.push({
                    index: index,
                    label: label,
                    element: th
                });
            }
        });

        if (columns.length === 0) return;

        // Create and insert the dropdown
        createColumnDropdown(columns, pageId);

        // Apply saved visibility
        applySavedVisibility(columns, pageId);
    });

    function getPageIdentifier() {
        // Use URL path + controller as unique identifier
        const params = new URLSearchParams(window.location.search);
        const controller = params.get('crudController') || params.get('crudControllerFqcn') || 'default';
        // Get just the class name from FQCN
        const controllerName = controller.split('\\').pop() || controller;
        return `columns_${controllerName}`;
    }

    function getColumnLabel(th, index) {
        // Skip checkbox column (batch actions)
        if (th.querySelector('input[type="checkbox"]')) return null;

        // Skip actions column (last column, usually empty header or "Actions")
        if (th.classList.contains('actions') ||
            th.classList.contains('actions-as-dropdown-table-head')) return null;

        // Get text from link or span
        const link = th.querySelector('a');
        const span = th.querySelector('span:not(.visually-hidden)');

        let label = '';
        if (link) {
            label = link.textContent.trim();
        } else if (span) {
            label = span.textContent.trim();
        } else {
            label = th.textContent.trim();
        }

        // If still empty or just whitespace, skip
        if (!label || label === '') {
            return null;
        }

        return label;
    }

    function createColumnDropdown(columns, pageId) {
        // Find the page actions area (where filters are)
        const pageActions = document.querySelector('.page-actions');
        if (!pageActions) {
            console.log('Column toggle: .page-actions not found');
            return;
        }

        // Find the datagrid-filters or global-actions to insert before
        const filtersDiv = pageActions.querySelector('.datagrid-filters');
        const globalActions = pageActions.querySelector('.global-actions');

        // Create dropdown container
        const dropdown = document.createElement('div');
        dropdown.className = 'column-toggle-dropdown datagrid-filters';
        dropdown.innerHTML = `
            <div class="btn-group">
                <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fa fa-columns"></i> Columns
                </button>
                <div class="dropdown-menu dropdown-menu-end column-dropdown-menu">
                    <div class="dropdown-header">
                        <span>Show/Hide Columns</span>
                        <button type="button" class="btn btn-sm btn-link reset-columns">Reset</button>
                    </div>
                    <div class="dropdown-divider"></div>
                    <div class="column-list">
                        ${columns.map(col => `
                            <label class="dropdown-item column-item">
                                <input type="checkbox" data-column-index="${col.index}" checked>
                                <span>${col.label}</span>
                            </label>
                        `).join('')}
                    </div>
                </div>
            </div>
        `;

        // Insert the dropdown
        if (filtersDiv) {
            filtersDiv.parentNode.insertBefore(dropdown, filtersDiv);
        } else if (globalActions) {
            globalActions.parentNode.insertBefore(dropdown, globalActions);
        } else {
            pageActions.insertBefore(dropdown, pageActions.firstChild);
        }

        // Add event listeners
        const checkboxes = dropdown.querySelectorAll('input[type="checkbox"]');
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function(e) {
                e.stopPropagation();
                const colIndex = parseInt(this.dataset.columnIndex);
                toggleColumn(colIndex, this.checked);
                saveVisibility(columns, pageId);
            });
        });

        // Reset button
        const resetBtn = dropdown.querySelector('.reset-columns');
        resetBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            checkboxes.forEach(cb => {
                cb.checked = true;
                const colIndex = parseInt(cb.dataset.columnIndex);
                toggleColumn(colIndex, true);
            });
            localStorage.removeItem(STORAGE_KEY + '_' + pageId);
        });

        // Prevent dropdown from closing when clicking inside
        dropdown.querySelector('.dropdown-menu').addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }

    function toggleColumn(columnIndex, visible) {
        const table = document.querySelector('table.datagrid');
        if (!table) return;

        // Toggle header
        const th = table.querySelector(`thead th:nth-child(${columnIndex + 1})`);
        if (th) {
            th.style.display = visible ? '' : 'none';
        }

        // Toggle all cells in that column
        const cells = table.querySelectorAll(`tbody td:nth-child(${columnIndex + 1})`);
        cells.forEach(cell => {
            cell.style.display = visible ? '' : 'none';
        });
    }

    function saveVisibility(columns, pageId) {
        const visibility = {};
        columns.forEach(col => {
            const checkbox = document.querySelector(`input[data-column-index="${col.index}"]`);
            if (checkbox) {
                visibility[col.index] = checkbox.checked;
            }
        });
        localStorage.setItem(STORAGE_KEY + '_' + pageId, JSON.stringify(visibility));
    }

    function applySavedVisibility(columns, pageId) {
        const saved = localStorage.getItem(STORAGE_KEY + '_' + pageId);
        if (!saved) return;

        try {
            const visibility = JSON.parse(saved);
            columns.forEach(col => {
                if (visibility.hasOwnProperty(col.index)) {
                    const isVisible = visibility[col.index];
                    toggleColumn(col.index, isVisible);

                    // Update checkbox
                    const checkbox = document.querySelector(`input[data-column-index="${col.index}"]`);
                    if (checkbox) {
                        checkbox.checked = isVisible;
                    }
                }
            });
        } catch (e) {
            console.error('Failed to parse saved column visibility:', e);
        }
    }
})();
