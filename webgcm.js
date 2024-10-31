/*
//
navigator.serviceWorker.register('firebase-messaging-sw.js')
	.then(function (registration) {
		console.log("Service Worker Registered");
		//msg.useServiceWorker(registration);
	}
	);

//
*/


//
//v9.22
//import { getMessaging, getToken } from "./firebase/messaging";
//https://www.gstatic.com/firebasejs/9.22.1/firebase-app.js
//import { getMessaging, getToken } from "https://www.gstatic.com/firebasejs/8.10.1/firebase-messaging.js";
//https://www.gstatic.com/firebasejs/9.22.1/firebase-messaging.js
//import { getMessaging, getToken } from "https://www.gstatic.com/firebasejs/9.22.1/firebase-messaging.js";

//v10.0
//
//https://stackoverflow.com/questions/74428972/firebase-cloud-messaging-for-web-does-not-receive-messages
//import { getMessaging, getToken } from "https://www.gstatic.com/firebasejs/9.14.0/firebase-messaging.js";
import { initializeApp } 
from "https://www.gstatic.com/firebasejs/10.0.0/firebase-app.js";
import { getMessaging, getToken, onMessage } 
from "https://www.gstatic.com/firebasejs/10.0.0/firebase-messaging.js";

//v8.10.1
//
//https://www.gstatic.com/firebasejs/8.10.1/firebase-messaging.js
//    import {getAnalytics} from "https://www.gstatic.com/firebasejs/10.0.0/firebase-analytics.js";
debugger;


///*
//import { initializeApp }
//	from "https://www.gstatic.com/firebasejs/8.10.1/firebase-app.js";
//import { getMessaging, getToken, onMessage }
//	from "https://www.gstatic.com/firebasejs/8.10.1/firebase-messaging.js";


//my test es6
//import { getMessaging, getToken } from "./myes6.js";
//import { getMessaging, getToken } from "./myfire/myes6.js";

const firebaseConfig =
{
	apiKey: "AIzaSyC53fS7mAuACHWoNU_rAUjlIPq75bBbLUk",
	authDomain: "testpwaappy.firebaseapp.com",
	projectId: "testpwaappy",
	storageBucket: "testpwaappy.appspot.com",
	messagingSenderId: "834872924553",
	appId: "1:834872924553:web:b57ad0032d877980fda958",
	measurementId: "G-44Y21Q7PE8"
};

// navigator.serviceWorker.ready
// 	.then(function (reg) {

// 		// Initialize Firebase
// 		const app = initializeApp(firebaseConfig);
// 		//const analytics = app.getAnalytics(app);
// 		const app2 = app;

// 		MsgTest();
// 	});

const app = initializeApp(firebaseConfig);
MsgTest();


async function MsgTest()
{
	// Get registration token. Initially this makes a network call, once retrieved
	// subsequent calls to getToken will return from cache.
	const msg = await getMessaging();

	//My SW::
	var bMySw=true;
	if (bMySw)
	{
		if ('serviceWorker' in navigator) {
			//websw
			//firebase-messaging-sw.js
			navigator.serviceWorker.register('websw.js')
				.then(function (registration) {
					console.log("Service Worker Registered");
					//msg.useServiceWorker(registration);
				}
				);
		}
		//return;
		await navigator.serviceWorker.ready;
	}

	// msg.requestPermission()
	// 	.then(function () {
	// 		console.log('Have permission');
	// 		return getToken(msg,
	// 		{
	// 			vapidKey: 'BIUuqVSsom5MGWMrHx4qCfGQYFXoWlcGGsc4Wo1hsiY30-2qwVnAef1h6O7GlUCVgBHKiwIIV8ezqiBqtFfusAY'
	// 		});
	// 	}
	// 	)
	// 	.then(function (token) {
	// 		console.log(token);
	// 	})
	// 	.catch(function (err) {
	// 		console.log('Error Ocurred');
	// 	})
		

	//firebase-messaging-sw.js
	//or websw.js
	//=> Service worker?????????
	getToken(msg,
		{
			vapidKey: 
			'BIUuqVSsom5MGWMrHx4qCfGQYFXoWlcGGsc4Wo1hsiY30-2qwVnAef1h6O7GlUCVgBHKiwIIV8ezqiBqtFfusAY'
		}
	)
	.then((currentToken) => {
		if (currentToken) {
			// Send the token to your server and update the UI if necessary
			// ...

			//foreground msg
			onMessage(function (payLoad) {
				console.log('onMessage ' + payLoad);
			});

		}
		else {
			// Show permission request UI
			console.log('No registration token available. Request permission to generate one.');
			// ...
		}
	})
	.catch((err) => {
		console.log('An error occurred while retrieving token. ', err);
		// ...
	});
}
//*/
