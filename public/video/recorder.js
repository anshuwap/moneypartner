(function (window) {
    var WORKER_PATH = '{{asset("video")}}recorderWorker.js';

    var Recorder = function (source, cfg) {
        var config = cfg || {};
        var bufferLen = config.bufferLen || 4096;
        this.context = source.context;
        // this.node = this.context.createJavaScriptNode(bufferLen, 2, 2);
        this.node = this.context.createScriptProcessor(2048, 1, 1);
        var worker = new Worker(config.workerPath || WORKER_PATH);
        worker.postMessage({
            command: "init",
            config: {
                sampleRate: this.context.sampleRate,
            },
        });
        var recording = false,
            currCallback;

        this.node.onaudioprocess = function (e) {
            if (!recording) return;
            worker.postMessage({
                command: "record",
                buffer: [
                    e.inputBuffer.getChannelData(0),
                    e.inputBuffer.getChannelData(1),
                ],
            });
        };

        this.configure = function (cfg) {
            for (var prop in cfg) {
                if (cfg.hasOwnProperty(prop)) {
                    config[prop] = cfg[prop];
                }
            }
        };

        this.record = function () {
            recording = true;
        };

        this.stop = function () {
            recording = false;
        };

        this.clear = function () {
            worker.postMessage({ command: "clear" });
        };

        this.getBuffer = function (cb) {
            currCallback = cb || config.callback;
            worker.postMessage({ command: "getBuffer" });
        };

        this.exportWAV = function (cb, type) {
            currCallback = cb || config.callback;
            type = type || config.type || "audio/wav";
            if (!currCallback) throw new Error("Callback not set");
            worker.postMessage({
                command: "exportWAV",
                type: type,
            });
        };

        worker.onmessage = function (e) {
            var blob = e.data;
            currCallback(blob);
        };

        source.connect(this.node);
        this.node.connect(this.context.destination); //this should not be necessary
    };

    Recorder.forceDownload = function (blob, filename) {
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "/record/saveaudio.php", true);
        xhr.onload = function (e) {
            console.log("loaded");
        };
        xhr.onreadystatechange = function () {
            console.log("state: " + xhr.readyState);
        };
        // Listen to the upload progress.
        xhr.send(blob);

        var url = (window.URL || window.webkitURL).createObjectURL(blob);
        var link = window.document.createElement("a");
        link.href = url;
        link.download = filename || "output.wav";
        var click = document.createEvent("Event");
        click.initEvent("click", true, true);
        link.dispatchEvent(click);
    };

    window.Recorder = Recorder;
})(window);
