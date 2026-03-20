const skillInput = document.getElementById('skillInput');
const suggestionBox = document.getElementById('skillSuggestions');

if (skillInput && suggestionBox) {
  let debounce;
  skillInput.addEventListener('input', () => {
    clearTimeout(debounce);
    const query = skillInput.value.split(',').pop().trim();
    if (query.length < 2) {
      suggestionBox.innerHTML = '';
      return;
    }

    debounce = setTimeout(async () => {
      const response = await fetch(`api/skills.php?q=${encodeURIComponent(query)}`);
      const data = await response.json();
      suggestionBox.innerHTML = data.data.map((item) => `<span class="badge badge-light border mr-1">${item.name}</span>`).join('');
    }, 250);
  });
}

document.querySelectorAll('.repeater').forEach((section) => {
  const container = section.querySelector('.repeater-container');
  const addButton = section.querySelector('.repeater-add');

  addButton?.addEventListener('click', () => {
    const firstItem = container.querySelector('.repeater-item');
    if (!firstItem) return;
    const clone = firstItem.cloneNode(true);
    const index = container.querySelectorAll('.repeater-item').length;
    clone.querySelectorAll('input').forEach((input) => {
      input.value = '';
      input.name = input.name.replace(/\[\d+\]/, `[${index}]`);
    });
    container.appendChild(clone);
  });

  container.addEventListener('click', (event) => {
    if (event.target.closest('.repeater-remove') && container.querySelectorAll('.repeater-item').length > 1) {
      event.target.closest('.repeater-item').remove();
    }
  });
});
