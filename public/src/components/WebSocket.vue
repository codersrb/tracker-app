<template>
</template>

<script>
    export default {
        data() {
            return {
                ws: null
            }
        },
        mounted () {
            let that = this;
            window.events.$on('wsConnect', function() {
                that.wsConnect()
            });

            window.events.$on('wsListen', function() {
                that.ws.onmessage = function(event) {
                    that.wsMessageHandler(event)
                }
            });


            window.events.$on('wsSend', function(data) {
                that.wsSend(data)
            });

            window.events.$on('wsLogout', function() {
                that.wsClose()
            })
        },
        methods: {

            /**
             * WebSocket Connect
             */
            wsConnect() {
                this.ws = new WebSocket('ws://tracker.mycodesamples.com:9090');

                this.ws.onopen = function() {
                    window.events.$emit('wsListen')
                };
            },

            /**
             * WebSocket Send data
             * @param data
             */
            wsSend(data) {
                let that = this;
                let jsonData = JSON.stringify(data);

                this.wsWaitForConnection(function() {
                    that.ws.send(jsonData)
                }, 500)
            },

            /**
             * WebSocket Close
             */
            wsClose() {
                this.ws.close()
            },

            /**
             * WebSocket in coming message handler
             * @param event
             */
            wsMessageHandler(event) {
                let data = JSON.parse(event.data);
                switch (data.action) {
                    case 'loggedOut' :
                        this.wsLoggedOut(data);
                        break;

                    case 'login' :
                        this.wsLogin(data);
                        break;

                    case 'getUsers' :
                        this.wsGetUsers(data);
                        break;
                }
            },

            /**
             * WebSocket Login Action
             * @param data
             */
            wsLogin(data) {
                window.events.$emit('appLogin', data)
            },

            /**
             * WebSocket Get User Action
             * @param data
             */
            wsGetUsers(data) {
                window.events.$emit('appGetUsers', data)
            },

            /**
             * WebSocket waits for a active connection
             * @param callback
             * @param interval
             */
            wsWaitForConnection(callback, interval) {
                if(this.ws.readyState === 1) {
                    callback()
                } else {
                    let that = this;
                    setTimeout(function() {
                        that.wsWaitForConnection(callback, interval)
                    }, interval)
                }
            }
        }
    }
</script>