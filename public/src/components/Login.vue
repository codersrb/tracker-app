<template>
    <form @submit.prevent="login()">
        <div class="form-group">
            <label for="exampleInputEmail1">Username</label>
            <input v-model="userName" required type="text" class="form-control" id="exampleInputEmail1" placeholder="Username">
        </div>
        <button type="submit" class="btn btn-outline-secondary">Login</button>
    </form>
</template>

<script>
    export default {
        props: {
            geoLocation: Object
        },
        data() {
            return {
                userName: null
            }
        },
        methods: {
            /**
             * Connects to WebSocket and Login to app
             */
            login() {
                window.events.$emit('wsConnect');
                window.events.$emit('wsSend', {
                    action: 'login',
                    userName: this.userName,
                    geo: this.geoLocation
                })
            }

        }
    }
</script>