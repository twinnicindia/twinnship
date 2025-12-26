document.addEventListener('DOMContentLoaded', function () {
    const totalRecords = 100;
    let recordsPerPage = 10;
    let currentPage = 1;

    function updatePagination() {
        const totalPages = Math.ceil(totalRecords / recordsPerPage);
        const startIndex = (currentPage - 1) * recordsPerPage + 1;
        const endIndex = Math.min(currentPage * recordsPerPage, totalRecords);
        document.querySelector('.page-info').textContent = `${currentPage} of ${totalPages}`;
        document.querySelector('.result-count').textContent = `Showing ${startIndex} to ${endIndex} of ${totalRecords} records.`;
        // Show/hide rows based on current page
        const rows = document.querySelectorAll('.card-row');
        rows.forEach((row, index) => {
            if (index >= startIndex - 1 && index < endIndex) {
                row.style.display = 'table-row';
            } else {
                row.style.display = 'none';
            }
        });
    }

    updatePagination();

    document.querySelector('.prev-page').addEventListener('click', function () {
        if (currentPage > 1) {
            currentPage--;
            updatePagination();
        }
    });

    document.querySelector('.next-page').addEventListener('click', function () {
        const totalPages = Math.ceil(totalRecords / recordsPerPage);
        if (currentPage < totalPages) {
            currentPage++;
            updatePagination();
        }
    });

    document.querySelector('.go-btn').addEventListener('click', function () {
        const inputPage = parseInt(document.querySelector('.go-to-page input').value);
        const totalPages = Math.ceil(totalRecords / recordsPerPage);
        if (inputPage >= 1 && inputPage <= totalPages) {
            currentPage = inputPage;
            updatePagination();
        } else {
            alert(`Please enter a page number between 1 and ${totalPages}.`);
        }
    });

    document.querySelector('.rows-per-page').addEventListener('change', function () {
        const selectedValue = this.value;
        if (selectedValue === 'All') {
            recordsPerPage = totalRecords;
            currentPage = 1;
        } else {
            recordsPerPage = parseInt(selectedValue);
            currentPage = 1;
        }
        updatePagination();
    });
});
