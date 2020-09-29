var config = {

    // When load 'requirejs' always load the following files also
    deps: [
        "js/local"
    ],
    urlArgs: "bust=" + (new Date()).getTime() // Disable require js cache

};