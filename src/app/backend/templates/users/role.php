<?php
$roles = ['' => '', 'reader' => _('Reader'),
    'editor' => _('Editor'), 'administrator' => _('Administrator')];
?>
{{ $form->label("role", "Roles") }}
{{ $form->select("role", $roles, onChange: "roleChanged()") }}
{{ $form->error("role") }}

{{ $form->label('claim[canAdd]', 'Add article') }}
{{ $form->checkbox('claim[canAdd]') }}
{{ $form->error('canAdd') }}

{{ $form->label('claim[canEdit]', 'Edit article') }}
{{ $form->checkbox('claim[canEdit]') }}
{{ $form->error('canEdit') }}

{{ $form->label('claim[canPublish]', 'Can publish') }}
{{ $form->checkbox('claim[canPublish]') }}
{{ $form->error('canPublish') }}

<script type="text/javascript">
    function roleChanged() {
        const role = document.getElementById("role").value;
        const addChb = document.getElementById("canAdd");
        const editChb = document.getElementById("canEdit");
        const publishChb = document.getElementById("canPublish");
        if (role == 'editor') {
            addChb.disabled = editChb.disabled = publishChb.disabled =  false;
        } else {
            addChb.checked = editChb.checked = publishChb.checked = false;
            addChb.disabled = editChb.disabled = publishChb.disabled = true;
        }
    }
    roleChanged();
</script>