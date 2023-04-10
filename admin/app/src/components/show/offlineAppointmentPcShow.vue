<template>
    <div>
        <div tag="offline_appointment_pc" class="offline_appointment_pc">
            <img src = "/static/admin/images/offline_appointment.png" style="width:100%;"/>
        </div>
    </div>
</template>

<script>
    import driver from './driver';
    export default {
        extends: driver,
        props:['element'],
    }
</script>

