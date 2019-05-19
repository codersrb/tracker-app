<template>
    <div class="row">
        <div class="col-md-4">
            <h5>Active Users
            <span v-if="users !== null">
                ({{ online }}/{{ online + offline }})
            </span>
            </h5>
            <ul class="list-group">
                <li v-if="users !== null" v-for="user in users" class="list-group-item">
                    <span v-if="user.online === true" class="badge badge-success badge-pill">&nbsp;</span>
                    <span v-else class="badge badge-danger badge-pill">&nbsp;</span>
                    {{ user.userName }}
                </li>
                <li v-else class="list-group-item">No active users</li>
            </ul>
        </div>
        <div class="col-md-7">
            <div id="map" ref="map"></div>
        </div>

        <div class="col-md-1">
            <a @click="logout()" class="btn btn-sm btn-outline-secondary" href="#">Logout</a>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                map: null,
                users: null,
                online: 0,
                offline: 0,
                markers: []
            }
        },
        mounted() {
            let that = this;
            this.map = new google.maps.Map(
                this.$refs.map, {
                    zoom: 5,
                    center: {lat: 21.7679, lng: 78.8718}
                });

            window.events.$on('appGetUsers', function(data) {
                that.users = data.data;

                that.online = 0;
                that.offline = 0;

                for(let key in that.users) {
                    if(that.users[key].online) {
                        that.online++
                    } else {
                        that.offline++
                    }
                    that.addMarker(that.users[key].geo, that.users[key].userName)
                }
            });
        },
        methods: {
            /**
             * Add Marker to map
             * @param geo
             * @param userName
             */
            addMarker(geo, userName) {
                let marker = new google.maps.Marker({
                    position: geo,
                    map: this.map,
                    label: userName,
                    title: userName,
                    animation: google.maps.Animation.DROP
                });

                let infowindow = new google.maps.InfoWindow({
                    content: userName
                });

                marker.addListener('click', function() {
                    infowindow.open(this.map, marker);
                });
            },
            /**
             * does an app logout and Websocket close
             */
            logout() {
                window.events.$emit('wsLogout');
                window.events.$emit('appLogout');
            }
        }
    }
</script>

<style scoped>

    #map {
        width: 100%;
        height: 500px;
    }




</style>