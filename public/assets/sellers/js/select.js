function initializeDropdown(dropdownId, selectedItemsContainerId, dropdownListId) {
    const dropdown = document.getElementById(dropdownId);
    const dropdownToggle = document.getElementById(`dropdownToggle_${dropdownId}`);
    const dropdownList = document.getElementById(`dropdownList_${dropdownId}`);
    const selectedItemsContainer = document.getElementById(selectedItemsContainerId);

    dropdownToggle.addEventListener('click', () => toggleDropdown(dropdownList));

    dropdownList.addEventListener('click', (e) => selectOption(e, selectedItemsContainer));

    function toggleDropdown(dropdownList) {
      dropdownList.style.display = dropdownList.style.display === 'block' ? 'none' : 'block';
    }

    function selectOption(e, selectedItemsContainer) {
      const selectedItem = e.target;
      const selectedValue = selectedItem.dataset.value;
      showSelectedItem(selectedValue, selectedItemsContainer);
      selectedItem.remove();
    }

    function showSelectedItem(value, selectedItemsContainer) {
      const item = document.createElement('div');
      item.classList.add('selected-item');
      item.innerHTML = `
          <span>${value}</span>
          <button onclick="removeSelectedItem(this.parentNode, '${value}')">&#10006;</button>
      `;
      selectedItemsContainer.appendChild(item);
    }

    function removeSelectedItem(item, value) {
      const dropdownListItem = document.createElement('div');
      dropdownListItem.classList.add('dropdown-list-item');
      dropdownListItem.dataset.value = value;
      dropdownListItem.textContent = value;
      dropdownList.appendChild(dropdownListItem);
      item.remove();
    }
  }

  initializeDropdown('order_status', 'selectedItems_order_status', 'dropdownList_order_status');
  initializeDropdown('payment_option', 'selectedItems_payment_option', 'dropdownList_payment_option');
  initializeDropdown('courier_partner', 'selectedItems_courier_partner', 'dropdownList_courier_partner');
  initializeDropdown('order_source', 'selectedItems_order_source', 'dropdownList_order_source');
  initializeDropdown('order_tag', 'selectedItems_order_tag', 'dropdownList_order_tag');
  initializeDropdown('pickup_address', 'selectedItems_pickup_address', 'dropdownList_pickup_address');