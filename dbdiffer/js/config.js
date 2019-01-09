// pi.debug.assert(!createConfig);

// update ctrls if initKeyArr is set.
function createConfig(path, initKeyArr, isNew) {
    var module = {};

    module.saveConfig = function () {
        var configStr = JSON.stringify(this.config);
        pi.wrap.vfs.createDirRecur(path, true);
        pi.file.write(path, configStr, "utf8");
    }

    module.saveCtrl = function (key) {
        this.config[key] = document.getElementById(key).value;
        this.saveConfig();
    }

    module.saveCtrls = function (keyArr) {
        keyArr = keyArr || initKeyArr;
        for (var i = 0; i < keyArr.length; i++) {
            var key = keyArr[i];
            var ele = document.getElementById(key)
            if(ele.type === "checkbox") {
                this.config[key] = ele.checked;
            }
            else if(ele.type === "select-one") {
                this.config[key] = ele.selectedIndex;
            }
            else {
                // "text", "textarea"...
                this.config[key] = ele.value;
            }
        };
        this.saveConfig();
    }

    module.restoreCtrl = function (key) {
        var ele = document.getElementById(key);
        var value = this.config[key];
        if(ele.type === "checkbox") {
            ele.checked = value;
        }
        else if(ele.type == "select-one") {
            ele.selectedIndex = parseInt(value);
        }
        else {
            // "text", "textarea"...
            ele.value = value || "";
        }
    }

    module.restoreCtrls = function (keyArr) {
        keyArr = keyArr || initKeyArr;
        for (var i = 0; i < keyArr.length; i++) {
            var key = keyArr[i];
            this.restoreCtrl(key);
        };
    }

    // load and init control values.
    function init(module) {
        module.config = {};
        if(isNew) {
            return;
        }
        if(path) {
            if(pi.file.exists(path)){
                var content = pi.file.read(path, "utf8");
                module.config = JSON.parse(content);
                pi.log("config loaded.")
            }
            else {
                pi.log("config load error, path not exists");
            }
        }
        else {
            pi.log("config load error.");
        }
        if(initKeyArr) {
            module.restoreCtrls(initKeyArr);
        }
    }
    init(module);
    return module;
}
