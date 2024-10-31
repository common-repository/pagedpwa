window.addEventListener('load',
    async function($)
    {
        var reg=await navigator.serviceWorker.ready;

        var subs=await reg.pushManager.getSubscription();
        if (!subs)
            return;
        await subs.unsubscribe();
        //ui
    }
);

