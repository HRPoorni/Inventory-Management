// Utility functions
const showNotification = (message, type = 'success') => {
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.textContent = message;
    document.body.appendChild(notification);
    notification.style.display = 'block';
    
    setTimeout(() => {
        notification.style.display = 'none';
        notification.remove();
    }, 3000);
};

const showLoading = () => {
    document.querySelector('.loading-spinner').style.display = 'block';
};

const hideLoading = () => {
    document.querySelector('.loading-spinner').style.display = 'none';
};

// Search functionality
const searchTable = () => {
    const input = document.querySelector('#searchInput');
    const filter = input.value.toLowerCase();
    const table = document.querySelector('.table');
    const rows = table.getElementsByTagName('tr');

    for (let i = 1; i < rows.length; i++) {
        let found = false;
        const cells = rows[i].getElementsByTagName('td');
        
        for (let j = 0; j < cells.length; j++) {
            const cellText = cells[j].textContent || cells[j].innerText;
            if (cellText.toLowerCase().indexOf(filter) > -1) {
                found = true;
                break;
            }
        }
        
        rows[i].style.display = found ? '' : 'none';
    }
};

// Sort table columns
const sortTable = (n) => {
    const table = document.querySelector('.table');
    let rows, switching, i, x, y, shouldSwitch, dir = 'asc';
    let switchcount = 0;
    switching = true;

    while (switching) {
        switching = false;
        rows = table.rows;

        for (i = 1; i < (rows.length - 1); i++) {
            shouldSwitch = false;
            x = rows[i].getElementsByTagName('td')[n];
            y = rows[i + 1].getElementsByTagName('td')[n];

            const xValue = isNaN(x.innerHTML) ? x.innerHTML.toLowerCase() : parseFloat(x.innerHTML);
            const yValue = isNaN(y.innerHTML) ? y.innerHTML.toLowerCase() : parseFloat(y.innerHTML);

            if (dir === 'asc') {
                if (xValue > yValue) {
                    shouldSwitch = true;
                    break;
                }
            } else if (dir === 'desc') {
                if (xValue < yValue) {
                    shouldSwitch = true;
                    break;
                }
            }
        }

        if (shouldSwitch) {
            rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
            switching = true;
            switchcount++;
        } else {
            if (switchcount === 0 && dir === 'asc') {
                dir = 'desc';
                switching = true;
            }
        }
    }
};

// Form validation
const validateForm = (formId) => {
    const form = document.getElementById(formId);
    const inputs = form.querySelectorAll('input[required], textarea[required]');
    let isValid = true;

    inputs.forEach(input => {
        if (!input.value.trim()) {
            input.classList.add('is-invalid');
            isValid = false;
        } else {
            input.classList.remove('is-invalid');
        }
    });

    return isValid;
};

// Initialize DataTable
const initializeDataTable = () => {
    if (typeof $.fn.DataTable !== 'undefined') {
        $('.table').DataTable({
            responsive: true,
            pageLength: 10,
            order: [[0, 'desc']],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search records..."
            }
        });
    }
};

// Handle quantity updates
const updateQuantity = async (itemId, newQuantity) => {
    try {
        showLoading();
        const response = await fetch('update_quantity.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ itemId, quantity: newQuantity })
        });

        if (!response.ok) throw new Error('Failed to update quantity');

        const data = await response.json();
        showNotification('Quantity updated successfully');
        return data;
    } catch (error) {
        showNotification(error.message, 'error');
    } finally {
        hideLoading();
    }
};

// Initialize tooltips and popovers
const initializeTooltips = () => {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl));
};

// Document ready
document.addEventListener('DOMContentLoaded', () => {
    // Initialize components
    initializeTooltips();
    initializeDataTable();

    // Add event listeners
    const searchInput = document.querySelector('#searchInput');
    if (searchInput) {
        searchInput.addEventListener('keyup', searchTable);
    }

    // Form submission handlers
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', (e) => {
            if (!validateForm(form.id)) {
                e.preventDefault();
                showNotification('Please fill in all required fields', 'error');
            }
        });
    });

    // Handle quantity change events
    const quantityInputs = document.querySelectorAll('.quantity-input');
    quantityInputs.forEach(input => {
        input.addEventListener('change', async (e) => {
            const itemId = e.target.dataset.itemId;
            const newQuantity = e.target.value;
            await updateQuantity(itemId, newQuantity);
        });
    });
});
