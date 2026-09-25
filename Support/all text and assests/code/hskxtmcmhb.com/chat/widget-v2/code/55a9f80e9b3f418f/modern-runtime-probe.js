(function () {
    /**
     * 此函数仅供浏览器解析动态 import 语法，不调用、不请求模块。
     * 构建必须原样输出整个文件，不能转译或移除未调用函数。
     */
    function supportsDynamicImport() {
        return import('./__ss_chat_dynamic_import_probe__.js');
    }

    /** 同时解析当前业务产物保留的异步生成器与异步迭代语法，不执行迭代。 */
    async function* supportsAsyncIteration() {
        for await (const value of []) {
            yield value;
        }
    }

    /** 实际执行业务使用的 Unicode 属性正则，避免只通过延迟解析。 */
    try {
        if (!/\p{Extended_Pictographic}/gu.test('\uD83D\uDE00')) return;
    } catch {
        return;
    }

    document.currentScript.setAttribute('data-ssc-runtime-supported', '1');
})();
