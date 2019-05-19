<template>
    <div id="app">
        <FlashMessage></FlashMessage>

        <Login v-if="geoLocation!==null && loggedInData == null"
               :geoLocation="geoLocation"></Login>
        <GoogleMap v-if="loggedInData != null"></GoogleMap>
        <WebSocket></WebSocket>
    </div>
</template>

<script>
    import Login from './components/Login.vue'
    import WebSocket from './components/WebSocket.vue'
    import GoogleMap from './components/GoogleMap.vue'

    export default {
        components: {
            Login,
            WebSocket,
            GoogleMap

        },
        data() {
            return {
                loggedInData : null,
                geoLocation: null
            }
        },

        mounted() {
            let that = this;
            window.events.$on('appLogin', function(data) {
                if(data.status === 'err') {
                    this.flashMessage.error({
                        title: 'App Error',
                        message: data.message
                    })
                } else {
                    that.loggedInData = data;
                    that.flashMessage.success({
                        title: 'Login Successful'});
                }
            });

            window.events.$on('appLogout', function() {
                that.loggedInData = null
            });

            this.getGeoLocation();
        },
        methods: {
            /**
             * Get clients GeoLocations
             */
            getGeoLocation() {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(this.saveGeoLocation, this.showGeoLocationError);
                } else {
                    this.flashMessage.error({
                        title: 'GeoLocation Error',
                        message: 'Geolocation is not supported by this browser.'
                    })
                }
            },
            /**
             * Saves client GeoLocation
             *
             * @param data
             */
            saveGeoLocation(data) {
                this.geoLocation = {
                    lat: data.coords.latitude,
                    lng: data.coords.longitude
                }
            },
            /**
             * Show Geo Location error
             * @param error
             */
            showGeoLocationError(error) {
                let data;
                switch(error.code) {
                    case error.PERMISSION_DENIED:
                        data = "User denied the request for Geolocation.";
                        break;
                    case error.POSITION_UNAVAILABLE:
                        data = "Location information is unavailable.";
                        break;
                    case error.TIMEOUT:
                        data = "The request to get user location timed out.";
                        break;
                    case error.UNKNOWN_ERROR:
                        data = "An unknown error occurred.";
                        break;
                }
                this.flashMessage.error({title: 'GeoLocation Error', message: data})
            }
        }
    }
</script>

<style>
    #app {
        font-size: 14px;
        font-family: 'Roboto', sans-serif;
    }
</style>