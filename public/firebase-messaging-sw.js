importScripts('https://www.gstatic.com/firebasejs/8.10.1/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.10.1/firebase-messaging.js');
let config = {
        apiKey: "AIzaSyBLVngaS_tDeMogfNmVEfqQ1u_HyqXMqc4",
        authDomain: "foodscan-5102b.firebaseapp.com",
        projectId: "foodscan-5102b",
        storageBucket: "foodscan-5102b.appspot.com",
        messagingSenderId: "1068326850326",
        appId: "1:1068326850326:web:fb724f0c9ae7f487ee4a37",
        measurementId: "G-8SFLD2GVEV",
 };
firebase.initializeApp(config);
const messaging = firebase.messaging();
messaging.onBackgroundMessage((payload) => {
    const notificationTitle = payload.notification.title;
    const notificationOptions = {
        body: payload.notification.body,
        icon: '/images/default/firebase-logo.png'
    };
    self.registration.showNotification(notificationTitle, notificationOptions);
});
