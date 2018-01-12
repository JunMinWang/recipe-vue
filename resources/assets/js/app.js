import Vue from 'vue';

import App from './App.vue';
import router from './router';

const app = new Vue({
    el: '#app',
    template: `<app></app>`,
    components: { App },
    router,
    mounted: function() {
        window.fbAsyncInit = function() {
            FB.init({
              appId            : '1161643790637319',
              autoLogAppEvents : true,
              xfbml            : true,
              version          : 'v2.11'
            });
          };
        
          (function(d, s, id){
             var js, fjs = d.getElementsByTagName(s)[0];
             if (d.getElementById(id)) {return;}
             js = d.createElement(s); js.id = id;
             js.src = "https://connect.facebook.net/zh-TW/sdk.js";
             fjs.parentNode.insertBefore(js, fjs);
           }(document, 'script', 'facebook-jssdk'));
    }
});
