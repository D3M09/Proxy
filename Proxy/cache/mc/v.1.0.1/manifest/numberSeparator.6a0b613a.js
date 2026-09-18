(function (window, document, $, tcg) {
  function resolveAllowDecimal(cfg) {
    if (cfg.allowDecimal === undefined || cfg.allowDecimal === null) {
      return cfg.decimal !== false;
    }
    return cfg.allowDecimal === "Y";
  }

  tcg.mc.easyNumberSeparator = (config) => {
    // Currency Separator
    const allowDecimal = resolveAllowDecimal(config);
    const obj = {
      selector: config.selector || ".number-separator",
      separator: config.separator || ",",
      decimalSeparator: config.decimalSeparator || ".",
      resultInput: config.resultInput,
      allowDecimal,
      decimal: allowDecimal,
      forceDecimal: tcg.mc.currency === "IDRK" || config.forceDecimal || false,
      maxIntegerDigits: tcg.mc.currency === "IDRK" ? 15 : null, // 限制整數位數
      listenPointer: config.listenPointer ?? true,
    };
    if (!obj.allowDecimal) {
      obj.forceDecimal = false;
    }
    function clampCaretToInteger(input, dotChar) {
      const v = input.value || "";
      const dot = v.indexOf(dotChar);
      if (dot === -1) return;
      const start = input.selectionStart ?? 0;
      const end = input.selectionEnd ?? 0;
      if (start > dot || end > dot) input.setSelectionRange(dot, dot);
    }

    function listenSelectionClamp() {
      const sel = obj.selector;

      $(document)
        .off("focusin.decimalClamp focusout.decimalClamp", sel)
        .on("focusin.decimalClamp", sel, function () {
          const el = this;
          const handler = function () {
            if (!obj.forceDecimal) return;
            if (document.activeElement !== el) return;
            clampCaretToInteger(el, tcg.mc.getDecimalSeparator());
          };
          $(el).data("decimalClampHandler", handler);
          document.addEventListener("selectionchange", handler, { passive: true });
        })
        .on("focusout.decimalClamp", sel, function () {
          const el = this;
          const handler = $(el).data("decimalClampHandler");
          if (handler) {
            document.removeEventListener("selectionchange", handler);
            $(el).removeData("decimalClampHandler");
          }
        });
    }

    function listenKeydownHard() {
      $(obj.selector)
        .off("keydown.decimalClamp")
        .on("keydown.decimalClamp", function (e) {
          if (!obj.forceDecimal) return;

          const el = this;
          const val = el.value || "";
          const dotChar = tcg.mc.getDecimalSeparator();
          const dot = val.indexOf(dotChar);
          if (dot === -1) return;

          const s = el.selectionStart ?? 0;
          const epos = el.selectionEnd ?? 0;
          const crosses = s < dot && epos > dot;
          const atOrAfterDot = s >= dot || crosses;

          const isDigit = (e.key >= "0" && e.key <= "9") || (e.code && /Numpad[0-9]/.test(e.code));
          const isDotKey = e.key === "." || e.key === ",";

          if (isDigit || isDotKey) {
            const atAfterDotStrict = s > dot || crosses;
            if (atAfterDotStrict) {
              e.preventDefault();
              el.setSelectionRange(dot, dot);
              return;
            }
          }

          if (e.key === "ArrowRight" || e.key === "End") {
            if (atOrAfterDot) {
              e.preventDefault();
              el.setSelectionRange(dot, dot);
            }
          }
        });
    }

    function listenKeydown() {
      $(obj.selector)
        .off("keydown.numberSeparator")
        .on("keydown.numberSeparator", function (e) {
          if (!obj.forceDecimal) return;
          const dotChar = tcg.mc.getDecimalSeparator();
          const val = this.value;
          const dotPos = val.indexOf(dotChar);
          const intPart = dotPos === -1 ? val : val.slice(0, dotPos);
          const intDigits = intPart.replace(/\D/g, "").length;
          const maxInt = obj.maxIntegerDigits;

          if (/^\d$/.test(e.key)) {
            if (dotPos === -1 || this.selectionStart <= dotPos) {
              if (maxInt && intDigits >= maxInt) {
                e.preventDefault();
                return;
              }
            }
          }
          if (dotPos === -1) return;
          const selectionStart = this.selectionStart;
          const selectionEnd = this.selectionEnd;
          if (e.key === "ArrowRight" || e.key === "End") {
            if (selectionStart > dotPos || (selectionStart < dotPos && selectionEnd > dotPos)) {
              e.preventDefault();
              this.setSelectionRange(dotPos, dotPos);
            }
          }
        });
    }

    function listenPointer() {
      $(obj.selector)
        .off("mouseup.numberSeparator click.numberSeparator select.numberSeparator touchend.numberSeparator")
        .on(
          "mouseup.numberSeparator click.numberSeparator select.numberSeparator touchend.numberSeparator",
          function () {
            if (!obj.forceDecimal) return;
            const el = this;
            const dotChar = tcg.mc.getDecimalSeparator();
            setTimeout(function () {
              clampCaretToInteger(el, dotChar);
            }, 0);
          }
        );
    }

    function numberSeparator(val, maxLength) {
      if (tcg.mc.currency === "IDRK") {
        obj.decimal = 3;
        obj.forceDecimal = true;
      }
      if (!val) {
        return "";
      }
      let num = val;
      const el = $(obj.selector).get(0);
      const dot = tcg.mc.getDecimalSeparator();
      const caret = el ? el.selectionStart : null;
      const dotIndex = val.indexOf(dot);
      const editingFraction = dotIndex !== -1 && caret !== null && caret > dotIndex;

      const endWithDecimal = num && num.endsWith(dot);
      const parseNum = tcg.mc.parseFormatNumber(num);
      const currentFracLen = dotIndex !== -1 ? (val.split(dot)[1] || "").length : 0;
      const decimalLength = obj.forceDecimal && !editingFraction ? 3 : Math.min(3, currentFracLen);

      if (String(parseNum).length > maxLength) {
        const truncated = String(parseNum).slice(0, maxLength);
        return tcg.mc.currencyFormat(truncated, decimalLength);
      }

      const decimal = obj.decimal ? decimalLength : 0;
      const value = tcg.mc.currencyFormat(parseNum, decimal);
      const regex = /^([^,]*,){2}[^,]*$/;
      const singleComma = !regex.test(val);
      if (endWithDecimal && tcg.mc.currency === "IDRK") return value;
      num = val === "-" ? null : value + (endWithDecimal && singleComma ? dot : "");

      if (tcg.mc.currency === "IDRK" && obj.forceDecimal && num && !editingFraction) {
        setTimeout(function () {
          const inputValue = $(obj.selector).val();
          const dotChar = tcg.mc.getDecimalSeparator();
          const dotIndexs = inputValue.indexOf(dotChar);
          let focusPos;
          if (dotIndexs !== -1) {
            focusPos = dotIndexs;
          } else {
            const commas = (inputValue.match(/,/g) || []).length;
            const intLen = String(parseNum).split(".")[0].length;
            focusPos = intLen + commas;
          }
          $(obj.selector).focus().get(0).setSelectionRange(focusPos, focusPos);
        }, 0);
      }

      return num;
    }
    function listenKeydownIntegerOnly() {
      $(obj.selector)
        .off("keydown.integerOnly")
        .on("keydown.integerOnly", function (e) {
          if (obj.allowDecimal) return;
          const dotChar = tcg.mc.getDecimalSeparator();
          if (e.key === dotChar || e.code === "NumpadDecimal") {
            e.preventDefault();
          }
        });
    }

    function numberSeparatorNormal(val) {
      if (!val) {
        return "";
      }
      let num = val;
      const dot = tcg.mc.getDecimalSeparator();
      const endWithDecimal = obj.allowDecimal && num && num.endsWith(dot);
      let parseNum = tcg.mc.parseFormatNumber(num);
      if (!obj.allowDecimal && Number.isFinite(parseNum)) {
        parseNum = Math.trunc(parseNum);
      }
      const decimalLength = obj.decimal ? Math.min(2, `${num}`.split(dot)[1]?.length || 0) : 0;
      const decimal = obj.decimal ? decimalLength : 0;
      const value = tcg.mc.currencyFormat(parseNum, decimal);
      const regex = /^([^,]*,){2}[^,]*$/;
      const singleComma = !regex.test(val);
      num = val === "-" ? null : value + (endWithDecimal && singleComma ? dot : "");

      return num;
    }

    function listenFields() {
      $(obj.selector)
        .off("input.numberSeparator")
        .on("input.numberSeparator", function (e) {
          const max = $(e.target).data("maxLength");
          this.value = obj.listenPointer ? numberSeparator(this.value, max) : numberSeparatorNormal(this.value);
          const parsedNum = tcg.mc.parseFormatNumber(this.value);
          const attrStr = typeof parsedNum === "number" && Number.isFinite(parsedNum) ? String(parsedNum) : "";
          this.setAttribute("formatamount", attrStr);
        });
    }

    listenFields();
    if (obj.listenPointer) {
      listenKeydown();
      listenPointer();
      listenKeydownHard();
      listenSelectionClamp();
    } else if (!obj.allowDecimal) {
      listenKeydownIntegerOnly();
    }
  };
})(window, document, jQuery, window.tcg || {});
