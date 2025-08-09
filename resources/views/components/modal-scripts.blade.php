<script>
    function showModal(id) {
        document.getElementById(id).classList.remove('hidden');
    }

    function hideModal(id) {
        document.getElementById(id).classList.add('hidden');
    }

    function closeModal(event, modal) {
        if (event.target === modal) {
            modal.classList.add('hidden');
        }
    }
</script>
