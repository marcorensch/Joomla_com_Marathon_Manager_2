document.addEventListener("DOMContentLoaded", ()=>{
    const parcoursFilter = document.getElementById('filter-parcours');
    const categoryFilter = document.getElementById('filter-category');
    const resultsTable = document.getElementById('results-table');
    const resultsTableBody = document.getElementById('results-table-body');
    const resultsTableRows = resultsTableBody.querySelectorAll('tr');
    const teamFilterInput = document.getElementById('filter-team');
    const resetBtn = document.getElementById('reset-filter-btn');

    categoryFilter.addEventListener("change", ()=>{doFiltering()});
    parcoursFilter.addEventListener("change", ()=>{doFiltering()});
    teamFilterInput.addEventListener("change", ()=>{doFiltering()});

    resetBtn.addEventListener("click", ()=>{
        categoryFilter.value = '';
        parcoursFilter.value = '';
        teamFilterInput.value = '';
        doFiltering("reset");
    });

    function doFiltering(from = "default"){
        const selectedCategory = categoryFilter.value;
        const selectedParcours = parcoursFilter.value;
        const teamFilterValue = teamFilterInput.value.trim().toLowerCase();

        if(selectedCategory === '' && selectedParcours === '' && teamFilterValue === ''){
            resultsTable.classList.add('uk-table-striped');
            resetBtn.classList.add('uk-disabled');
        }else{
            resultsTable.classList.remove('uk-table-striped');
            resetBtn.classList.remove('uk-disabled');
        }

        resultsTableRows.forEach((row)=>{
            let isVisible = true;
            if(selectedCategory === '' && selectedParcours === '' && teamFilterValue === ''){
                // No filter is set, show all
                row.classList.remove('filtered-hidden');
            }else{
                isVisible = calcVisibleState(row, selectedParcours, selectedCategory, teamFilterValue );
            }

            // Set the effective hidden state
            if(isVisible){
                row.classList.remove('filtered-hidden');
            }else{
                row.classList.add('filtered-hidden');
            }
        });
        resetStripedTable();
    }

    function calcVisibleState(element, selectedParcours, selectedCategory, teamFilterValue)
    {

        if(teamFilterValue !== ''){
            if(!element.dataset.team.toLowerCase().trim().includes(teamFilterValue)) return false;
        }

        if(selectedParcours !== ''){
            if(element.dataset.parcours !== selectedParcours) return false;
        }

        if(selectedCategory !== '') {
            if(element.dataset.category !== selectedCategory) return false;
        }

        return true;
    }


    function resetStripedTable(){
        const visibleElements = resultsTableBody.querySelectorAll('tr:not(.filtered-hidden)');
        visibleElements.forEach((row, index)=>{
            if(index % 2 === 0){
                row.classList.add('nxd-table-middle');
            }else{
                row.classList.remove('nxd-table-middle');
            }
        });
    }
})