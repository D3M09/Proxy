(function (window, document, $, tcg) {
  tcg.ac.easyNumberSeparator = (config) => {
    // Currency Separator
    const obj = {
      selector: config.selector || ".number-separator",
      separator: config.separator || ",",
      decimalSeparator: config.decimalSeparator || ".",
      resultInput: config.resultInput,
      decimal: config.decimal || true,
    };
    function numberSeparator(val) {
      if (!val) {
        return "";
      }
      let num = val;
      const dot = tcg.ac.getDecimalSeparator();
      const endWithDecimal = num && num.endsWith(dot);
      const parseNum = tcg.ac.parseFormatNumber(num);
      const decimalLength = Math.min(2, `${num}`.split(dot)[1]?.length) || 0;
      const decimal = obj.decimal ? decimalLength : 0;
      const value = tcg.ac.currencyFormat(parseNum, decimal);
      const regex = /^([^,]*,){2}[^,]*$/;
      const singleComma = !regex.test(val);
      num = val === "-" ? null : value + (endWithDecimal && singleComma ? dot : "");

      return num;
    }

    function listenFields() {
      $(obj.selector).on("input", function (e) {
        this.value = numberSeparator(this.value);
        this.setAttribute("formatAmount", tcg.ac.parseFormatNumber(this.value));
      });
    }

    listenFields();
  };
})(window, document, jQuery, window.tcg || {});
