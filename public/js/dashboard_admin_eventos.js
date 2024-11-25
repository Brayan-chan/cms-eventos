document.addEventListener('DOMContentLoaded', function() {
    const editEventBtns = document.querySelectorAll('.editEventBtn');
    const eventModal = document.getElementById('eventModal');
    const closeModalBtn = document.getElementById('closeModalBtn');

    editEventBtns.forEach(btn => {
        btn.addEventListener('click', async () => {
            const eventId = btn.dataset.id;
            const response = await axios.get(`../controllers/event_actions.php?action=get_event&id=${eventId}`);
            const event = response.data;

            // Rellenar los campos del formulario con los datos del evento
            document.getElementById('editEventId').value = event.id;
            document.getElementById('editTitle').value = event.title;
            document.getElementById('editDate').value = event.date.replace(' ', 'T');
            document.getElementById('editLocation').value = event.location;
            document.getElementById('editDescription').value = event.description;
            document.getElementById('editIsPublished').checked = event.is_published === "1";

            // Mostrar el modal
            eventModal.classList.add('active');
        });
    });

    // Ocultar el modal
    closeModalBtn.addEventListener('click', () => {
        eventModal.classList.remove('active');
    });
});