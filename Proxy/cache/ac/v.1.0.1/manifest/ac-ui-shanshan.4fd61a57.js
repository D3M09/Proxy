(function (window, document, $, tcg) {
  // Agent Center UI Events
  tcg.ac.ui = {};
  tcg.ac.ui.loadRebateQuota = function (quotas) {
    globalVar.quotaObj = [];
    globalVar.quotaObj.length = 0;
    var _html = "";
    _html += '<ul id="dira-regdline-quota-tabs">';
    if (quotas.length > 0) {
      globalVar.quotaObj = quotas;
      for (var i = 0; i < quotas.length; i++) {
        _html += "<li class='clearfix'>";
        _html +=
          "<input type='radio' name='dira-regdline-rquota' id='rquota" +
          quotas[i].quotaId +
          "' disabled='disabled' class='disabled' quota-id='" +
          quotas[i].quotaId +
          "' value='0'>";
        _html +=
          "<label for='rquota" +
          quotas[i].quotaId +
          "' class='disabled' disabled='disabled'>" +
          quotas[i].templateName +
          " / " +
          quotas[i].quotaRemaining +
          "个</label>";
        _html += "</li>";
      }
      _html += "</ul>";
      $("#qoutaWrapper").removeClass("hide");
    } else {
      $("#qoutaWrapper").addClass("hide");
    }
    $("#rebateQuota").html(_html);
  };

  tcg.ac.ui.agentInformation = function (rs) {
    var agentInfoResult = rs.value;
    var basic_html = "";
    basic_html +=
      '<div class="dac_div_rscontent">' +
      '<div class="div-sq dac_div_rstitle"><span>' +
      tcg.lib.prop("ac_agent_info_table_1") +
      "</span></div>" +
      '<div class="dac_div_rsq">' +
      '<div class="div-sq1">' +
      '<div class="cirle-info">' +
      (agentInfoResult.linearLower || "-") +
      "</div>" +
      "<p>" +
      tcg.lib.prop("ac_agent_info_table_1_column_1") +
      "</p>" +
      "</div>" +
      '<div class="div-sq1">' +
      '<div class="cirle-info">' +
      (agentInfoResult.teamNumber || "-") +
      "</div>" +
      "<p>" +
      tcg.lib.prop("ac_agent_info_table_1_column_2") +
      "</p>" +
      "</div>" +
      '<div class="div-sq1">' +
      '<div class="cirle-info">' +
      (agentInfoResult.todayRegNum || "-") +
      "</div>" +
      "<p>" +
      tcg.lib.prop("ac_agent_info_table_1_column_3") +
      "</p>" +
      "</div>" +
      '<div class="div-sq1">' +
      '<div class="cirle-info">' +
      (agentInfoResult.totalDepositCount || "-") +
      "</div>" +
      "<p>" +
      tcg.lib.prop("ac_agent_info_table_1_column_4") +
      "</p>" +
      "</div>" +
      '<div class="div-sq1">' +
      '<div class="cirle-info">' +
      (agentInfoResult.todayFirstDepositMemberCounter || "-") +
      "</div>" +
      "<p>" +
      tcg.lib.prop("ac_agent_info_table_1_column_5") +
      "</p>" +
      "</div>" +
      "</div>" +
      "</div>";
    basic_html +=
      '<div class="dac_div_rscontent">' +
      '<div class="div-sq dac_div_rstitle"><span>' +
      tcg.lib.prop("ac_agent_info_table_2") +
      "</span></div>" +
      '<div class="dac_div_rsq">' +
      '<div class="div-sq1">' +
      '<div class="cirle-info">' +
      (agentInfoResult.todayRebate || "-") +
      "</div>" +
      "<p>" +
      tcg.lib.prop("ac_agent_info_table_2_column_1") +
      "</p>" +
      "</div>" +
      '<div class="div-sq1">' +
      '<div class="cirle-info">' +
      (agentInfoResult.todaylottPlayerRebate || "-") +
      "</div>" +
      "<p>" +
      tcg.lib.prop("ac_agent_info_table_2_column_2") +
      "</p>" +
      "</div>" +
      '<div class="div-sq1">' +
      '<div class="cirle-info">' +
      (agentInfoResult.todaylivePlayerRebate || "-") +
      "</div>" +
      "<p>" +
      tcg.lib.prop("ac_agent_info_table_2_column_3") +
      "</p>" +
      "</div>" +
      '<div class="div-sq1">' +
      '<div class="cirle-info">' +
      (agentInfoResult.todayrngPlayerRebate || "-") +
      "</div>" +
      "<p>" +
      tcg.lib.prop("ac_agent_info_table_2_column_4") +
      "</p>" +
      "</div>" +
      '<div class="div-sq1">' +
      '<div class="cirle-info">' +
      (agentInfoResult.todaysportPlayerRebate || "-") +
      "</div>" +
      "<p>" +
      tcg.lib.prop("ac_agent_info_table_2_column_5") +
      "</p>" +
      "</div>" +
      "</div>" +
      "</div>";
    basic_html +=
      '<div class="dac_div_rscontent">' +
      '<div class="div-sq dac_div_rstitle"><span>' +
      tcg.lib.prop("ac_agent_info_table_3") +
      "</span></div>" +
      '<div class="dac_div_rsq">' +
      '<div class="div-sq1">' +
      '<div class="cirle-info">' +
      (agentInfoResult.rebate_SSC_1 || "-") +
      "</div>" +
      "<p>" +
      tcg.lib.prop("ac_agent_info_table_3_column_1") +
      "</p>" +
      "</div>" +
      '<div class="div-sq1">' +
      '<div class="cirle-info">' +
      (agentInfoResult.rebate_11X5_1 || "-") +
      "</div>" +
      "<p>" +
      tcg.lib.prop("ac_agent_info_table_3_column_2") +
      "</p>" +
      "</div>" +
      '<div class="div-sq1">' +
      '<div class="cirle-info">' +
      (agentInfoResult.rebate_PK10_1 || "-") +
      "</div>" +
      "<p>" +
      tcg.lib.prop("ac_agent_info_table_3_column_3") +
      "</p>" +
      "</div>" +
      '<div class="div-sq1">' +
      '<div class="cirle-info">' +
      (agentInfoResult.rebate_LF_1 || "-") +
      "</div>" +
      "<p>" +
      tcg.lib.prop("ac_agent_info_table_3_column_4") +
      "</p>" +
      "</div>" +
      '<div class="div-sq1">' +
      '<div class="cirle-info">' +
      (agentInfoResult.rebate_LHC_1 == undefined ? "-" : agentInfoResult.rebate_LHC_1) +
      "</div>" +
      "<p>" +
      tcg.lib.prop("ac_agent_info_table_3_column_11") +
      "</p>" +
      "</div>" +
      '<div class="div-sq1">' +
      '<div class="cirle-info">' +
      (agentInfoResult.rebate_PVP == undefined ? "-" : agentInfoResult.rebate_PVP * 0.025 + "%") +
      "</div>" +
      "<p>" +
      tcg.lib.prop("ac_agent_info_table_3_column_6") +
      "</p>" +
      "</div>" +
      '<div class="div-sq1">' +
      '<div class="cirle-info">' +
      (agentInfoResult.rebate_LIVE == undefined ? "-" : agentInfoResult.rebate_LIVE + "%") +
      "</div>" +
      "<p>" +
      tcg.lib.prop("ac_agent_info_table_3_column_7") +
      "</p>" +
      "</div>" +
      '<div class="div-sq1">' +
      '<div class="cirle-info">' +
      (agentInfoResult.rebate_RNG == undefined ? "-" : agentInfoResult.rebate_RNG + "%") +
      "</div>" +
      "<p>" +
      tcg.lib.prop("ac_agent_info_table_3_column_8") +
      "</p>" +
      "</div>" +
      '<div class="div-sq1">' +
      '<div class="cirle-info">' +
      (agentInfoResult.rebate_SPORTS == undefined ? "-" : agentInfoResult.rebate_SPORTS + "%") +
      "</div>" +
      "<p>" +
      tcg.lib.prop("ac_agent_info_table_3_column_9") +
      "</p>" +
      "</div>" +
      '<div class="div-sq1">' +
      '<div class="cirle-info">' +
      (agentInfoResult.rebate_FISH == undefined ? "-" : agentInfoResult.rebate_FISH + "%") +
      "</div>" +
      "<p>" +
      tcg.lib.prop("ac_agent_info_table_3_column_10") +
      "</p>" +
      "</div>" +
      "</div>" +
      "</div>";
    $("#dac_basic_info").append(basic_html);
    $("#dac_safety_info").append(
      '<div class="infor1"><p>' +
        tcg.lib.prop("ac_agent_info_table_4") +
        "</p><p>" +
        agentInfoResult.totalDepositMoney.toFixed(2) +
        '</p></div><div class="infor2"><p>' +
        tcg.lib.prop("ac_agent_info_table_5") +
        "</p><p>" +
        tcg.lib.prop("ac_agent_info_table_5_column_1") +
        "</p><p>" +
        agentInfoResult.logonTime +
        "</p><p>" +
        tcg.lib.prop("ac_agent_info_table_5_column_2") +
        "</p><p>" +
        agentInfoResult.logonIP +
        "</p></div>"
    );
  };
  tcg.ac.ui.agentInformationCopy = function (rs) {
    var agentInfoResult = rs.value;
    var basic_html = "";
    basic_html +=
      '<div class="dac_div_rscontent">' +
      '<div class="div-sq dac_div_rstitle"><span>' +
      tcg.lib.prop("ac_agent_info_table_1") +
      "</span></div>" +
      '<div class="dac_div_rsq">' +
      '<div class="div-sq1">' +
      '<div class="cirle-info">' +
      (agentInfoResult.linearLower || "-") +
      "</div>" +
      "<p>" +
      tcg.lib.prop("ac_agent_info_table_1_column_1") +
      "</p>" +
      "</div>" +
      '<div class="div-sq1">' +
      '<div class="cirle-info">' +
      (agentInfoResult.teamNumber || "-") +
      "</div>" +
      "<p>" +
      tcg.lib.prop("ac_agent_info_table_1_column_2") +
      "</p>" +
      "</div>" +
      '<div class="div-sq1">' +
      '<div class="cirle-info">' +
      (agentInfoResult.todayRegNum || "-") +
      "</div>" +
      "<p>" +
      tcg.lib.prop("ac_agent_info_table_1_column_3") +
      "</p>" +
      "</div>" +
      '<div class="div-sq1">' +
      '<div class="cirle-info">' +
      (agentInfoResult.totalDepositCount || "-") +
      "</div>" +
      "<p>" +
      tcg.lib.prop("ac_agent_info_table_1_column_4") +
      "</p>" +
      "</div>" +
      '<div class="div-sq1">' +
      '<div class="cirle-info">' +
      (agentInfoResult.todayFirstDepositMemberCounter || "-") +
      "</div>" +
      "<p>" +
      tcg.lib.prop("ac_agent_info_table_1_column_5") +
      "</p>" +
      "</div>" +
      "</div>" +
      "</div>";
    /*basic_html += '<div class="dac_div_rscontent">' +
            '<div class="div-sq dac_div_rstitle"><span>' +tcg.lib.prop("ac_agent_info_table_2")+ '</span></div>' +
            '<div class="dac_div_rsq">' +
            '<div class="div-sq1">' +
            '<div class="cirle-info">' + (agentInfoResult.todayRebate || "-") + '</div>' +
            '<p>' +tcg.lib.prop("ac_agent_info_table_2_column_1")+ '</p>' +
            '</div>' +
            '<div class="div-sq1">' +
            '<div class="cirle-info">' + (agentInfoResult.todaylottPlayerRebate || "-") + '</div>' +
            '<p>' +tcg.lib.prop("ac_agent_info_table_2_column_2")+ '</p>' +
            '</div>' +
            '<div class="div-sq1">' +
            '<div class="cirle-info">' + (agentInfoResult.todaylivePlayerRebate || "-") + '</div>' +
            '<p>' +tcg.lib.prop("ac_agent_info_table_2_column_3")+ '</p>' +
            '</div>' +
            '<div class="div-sq1">' +
            '<div class="cirle-info">' + (agentInfoResult.todayrngPlayerRebate || "-") + '</div>' +
            '<p>' +tcg.lib.prop("ac_agent_info_table_2_column_4")+ '</p>' +
            '</div>' +
            '<div class="div-sq1">' +
            '<div class="cirle-info">' + (agentInfoResult.todaysportPlayerRebate || "-") + '</div>' +
            '<p>' +tcg.lib.prop("ac_agent_info_table_2_column_5")+ '</p>' +
            '</div>' +
            '</div>' +
            '</div>';
        basic_html += '<div class="dac_div_rscontent">' +
            '<div class="div-sq dac_div_rstitle"><span>' +tcg.lib.prop("ac_agent_info_table_3")+ '</span></div>' +
            '<div class="dac_div_rsq">' +
            '<div class="div-sq1">' +
            '<div class="cirle-info">' + (agentInfoResult.rebate_SSC_1 || "-") + '</div>' +
            '<p>' +tcg.lib.prop("ac_agent_info_table_3_column_1")+ '</p>' +
            '</div>' +
            '<div class="div-sq1">' +
            '<div class="cirle-info">' + (agentInfoResult.rebate_11X5_1 || "-") + '</div>' +
            '<p>' +tcg.lib.prop("ac_agent_info_table_3_column_2")+ '</p>' +
            '</div>' +
            '<div class="div-sq1">' +
            '<div class="cirle-info">' + (agentInfoResult.rebate_PK10_1 || "-") + '</div>' +
            '<p>' +tcg.lib.prop("ac_agent_info_table_3_column_3")+ '</p>' +
            '</div>' +
            '<div class="div-sq1">' +
            '<div class="cirle-info">' + (agentInfoResult.rebate_LF_1 || "-") + '</div>' +
            '<p>' +tcg.lib.prop("ac_agent_info_table_3_column_4")+ '</p>' +
            '</div>' +
            '<div class="div-sq1">' +
            '<div class="cirle-info">' + (agentInfoResult.rebate_ELOTTO == undefined ? "-" : agentInfoResult.rebate_ELOTTO+"%") + '</div>' +
            '<p>' +tcg.lib.prop("ac_agent_info_table_3_column_5")+ '</p>' +
            '</div>' +
            '<div class="div-sq1">' +
            '<div class="cirle-info">' + (agentInfoResult.rebate_PVP == undefined ? "-" : (agentInfoResult.rebate_PVP * 0.025)+"%") + '</div>' +
            '<p>' +tcg.lib.prop("ac_agent_info_table_3_column_6")+ '</p>' +
            '</div>' +
            '<div class="div-sq1">' +
            '<div class="cirle-info">' + (agentInfoResult.rebate_LIVE == undefined ? "-" : agentInfoResult.rebate_LIVE+"%") + '</div>' +
            '<p>' +tcg.lib.prop("ac_agent_info_table_3_column_7")+ '</p>' +
            '</div>' +
            '<div class="div-sq1">' +
            '<div class="cirle-info">' + (agentInfoResult.rebate_RNG == undefined ? "-" : agentInfoResult.rebate_RNG+"%") + '</div>' +
            '<p>' +tcg.lib.prop("ac_agent_info_table_3_column_8")+ '</p>' +
            '</div>' +
            '<div class="div-sq1">' +
            '<div class="cirle-info">' + (agentInfoResult.rebate_SPORTS == undefined ? "-" : agentInfoResult.rebate_SPORTS+"%") + '</div>' +
            '<p>' +tcg.lib.prop("ac_agent_info_table_3_column_9")+ '</p>' +
            '</div>' +
            '<div class="div-sq1">' +
            '<div class="cirle-info">' + (agentInfoResult.rebate_FISH == undefined ? "-" : agentInfoResult.rebate_FISH+"%") + '</div>' +
            '<p>' +tcg.lib.prop("ac_agent_info_table_3_column_10")+ '</p>' +
            '</div>' +
            '</div>' +
            '</div>';*/
    $("#dac_basic_info").append(basic_html);
    $("#dac_safety_info").append(
      '<div class="infor1"><p>' +
        tcg.lib.prop("ac_agent_info_table_4") +
        "</p><p>" +
        agentInfoResult.totalDepositMoney.toFixed(2) +
        '</p></div><div class="infor2"><p>' +
        tcg.lib.prop("ac_agent_info_table_5") +
        "</p><p>" +
        tcg.lib.prop("ac_agent_info_table_5_column_1") +
        "</p><p>" +
        agentInfoResult.logonTime +
        "</p><p>" +
        tcg.lib.prop("ac_agent_info_table_5_column_2") +
        "</p><p>" +
        agentInfoResult.logonIP +
        "</p></div>"
    );
  };
  tcg.ac.ui.onRegTypeSwitch = function (form) {
    $(document)
      .off("click", form + " .new-switch-con")
      .on("click", form + " .new-switch-con", function () {
        var selected = $(this).find(".selected");
        selected.siblings().addClass("selected");
        selected.siblings().removeClass("hide");
        selected.removeClass("selected");
        selected.addClass("hide");
      });
  };
  tcg.ac.ui.onQuotaSwitch = function (form) {
    $(document)
      .off("change", form + " #rQuotaSwitch")
      .on("change", form + " #rQuotaSwitch", function () {
        tcg.ac.ui.rQuotaSwitch(form, "#dira-regdline-quota-tabs", ".dira-regdline-gb-list");
      });
  };
  tcg.ac.ui.customSelect = function (id) {
    var prop = {
      width: "100%",
      disable_search: true,
    };
    $(id)
      .chosen(prop)
      .on("chosen:showing_dropdown", function () {
        $(this).parents("div").addClass("red-up");
      })
      .on("chosen:hiding_dropdown", function () {
        $(this).parents("div").removeClass("red-up");
      });
  };
  tcg.ac.ui.register_downline_confirmation = function (data) {
    var TYPE = [tcg.lib.prop("label_member"), tcg.lib.prop("label_agent")];
    var _html = "";
    _html += '<div class="bet_content">';
    _html += "    <p>" + tcg.lib.prop("ac_bet_content") + "</p>";
    _html += '   <ul class="">';
    _html += "   <li><span>" + tcg.lib.prop("ac_bet_content_1") + " : </span><span>" + TYPE[data.type] + "</span></li>";
    _html +=
      "   <li><span>" +
      tcg.lib.prop("label_registration_account") +
      " : </span><span>" +
      data.username +
      "</span></li>";
    _html +=
      "   <li><span>" +
      tcg.lib.prop("label_pwd_login") +
      " : </span><span>" +
      (data.password ? data.password : "123456") +
      "</span></li>";

    if ($.trim(data.nickname) !== "") {
      _html += "<li><span>" + tcg.lib.prop("label_remark") + " : </span><span>" + data.nickname + "</span></li>";
    }

    for (var i = 0; i < data.rebateSettingConfigs.length; i++) {
      var e = data.rebateSettingConfigs[i];
      _html +=
        "<li><span>" +
        tcg.ac.lotto_translate(e.type) +
        ": </span><span>" +
        ((e.rebateValue + "").indexOf(".") > -1 ? e.rebateValue.toFixed(2) : e.rebateValue) +
        "</span></li>";
    }
    _html += "</ul>";
    _html += "</div>";
    return _html;
  };

  tcg.ac.ui.registerDownlineSetQuta = function (id, rs, _let, data) {
    var _html = "";
    $(id).empty();
    $.each(rs, function (key, vl) {
      if (data[key] != undefined && data[key] != "") {
        _html += '<div id="deleQutarebateEache' + _let + key + '" class="dele_qutarebate_eache ' + key + '">';
        _html += '<div id="hagDlqutaList' + key + '" class="hag_dlquta_list">';
        _html += '<div class="hag_register_commun hag_register_communnf hac_link_mangslc">';
        _html +=
          '<span class="hag_regcom_lt hag_reg_fc142">' +
          tcg.lib.prop(key.toUpperCase()) +
          tcg.lib.prop("ac_quota") +
          "</span>";
        _html += '<div class="switch-conmg switch-conmg-quta">';
        _html += '<div class="onoffswitch">';
        _html +=
          '<input type="checkbox" name="reg_rebateType' +
          _let +
          '" data-value="' +
          key +
          '" class="onoffswitch-checkbox" id="reg_qutaType' +
          _let +
          key +
          '">';
        _html += '<label class="onoffswitch-label" for="reg_qutaType' + _let + key + '">';
        _html += '<span class="onoffswitch-inner"></span>';
        _html += '<span class="onoffswitch-switch"></span>';
        _html += "</label>";
        _html += "</div>";
        _html += "</div>";
        _html += "</div>";
        _html += '<div id="hagDlqutaSelect' + _let + key + '" class="hag_register_commun hac_link_mangslc hide">';
        _html += '<span class="hag_regcom_lt hag_reg_fc142">' + tcg.lib.prop("ac_select_quota") + "</span>";
        _html +=
          '<div class="hag_regcom_nameinp hag_regcom_nameinsl customquta_dropdown"><input type="text" name="qutaId" value="" data-value="" placeholder="' +
          tcg.lib.prop("ac_select_quota_placeholder") +
          '" readonly="readonly">';
        _html += '<div class="hag_regcom_tmslcac hide">';
        _html += '<ul class="chosen-results1 hag_alluse_scroll" id="hagDlqutaSelectList' + key + '">';
        $.each(vl, function (idx, data) {
          _html +=
            '<li value="' +
            data.quotaId +
            '" data-type="' +
            key +
            '" data-num="' +
            idx +
            '">' +
            data.templateName +
            "/" +
            data.quotaRemaining +
            "个</li>";
        });
        _html += "</ul>";
        _html += "</div>";
        _html += "</div>";
        _html += "</div>";
        _html += "</div>";
        _html += data[key];
        _html += "</div>";
      }
    });
    $(id).append(_html);
  };

  tcg.ac.ui.quotaNameList = function (data_rs) {
    var _html = "";
    for (var i = 0; i < data_rs.value.length; i++) {
      _html +=
        '<li value="' +
        data_rs.value[i].quotaId +
        '">' +
        data_rs.value[i].templateName +
        "/" +
        data_rs.value[i].quotaRemaining +
        tcg.lib.prop("label_unite_ge") +
        "</li>";
    }
    $("#quotaNameList").html(_html);
  };

  tcg.ac.ui._changeSlideText = function (elem, ui) {
    var maxSeries = $(elem).attr("max"),
      group_code = $(elem).attr("data-type"),
      minSeries = $(elem).attr("min"),
      rebate_interval_item = ui.value;
    $(elem).attr("data-value", ui.value);

    if ($(elem).hasClass("lotto_slider_controller")) {
      $(elem)
        .parent()
        .find("span")
        .html((rebate_interval_item + "").indexOf(".") > -1 ? rebate_interval_item.toFixed(2) : rebate_interval_item);
    } else {
      $(elem)
        .parent()
        .find("span")
        .html(
          ((rebate_interval_item + "").indexOf(".") > -1 ? rebate_interval_item.toFixed(2) : rebate_interval_item) +
            tcg.ac.rebateTransformOther(rebate_interval_item, group_code, minSeries)
        );
    }
    var val = (ui.value - $(elem).attr("min")) / ($(elem).attr("max") - $(elem).attr("min"));

    $(elem).find("input").attr("data-value", ui.value);
    $(elem).find("input").attr("value", ui.value);

    $(elem).css("background", "");
    $(elem).css(
      "background-image",
      "-webkit-gradient(linear, left top, right top, " +
        "color-stop(" +
        val +
        ", rgb(100, 200, 20)), " +
        "color-stop(" +
        val +
        ", #C5C5C5)" +
        ")"
    );
  };

  tcg.ac.ui._build_slider = function (id) {
    $.each($("" + id + " .range_slider_rebate"), function () {
      $(this).slider({
        value: $(this).attr("value") * 1,
        min: $(this).attr("min") * 1,
        max: $(this).attr("max") * 1,
        step: $(this).attr("data-steps") * 1,
        slide: function (event, ui) {
          tcg.ac.ui._changeSlideText(this, ui);
          if ($(this).hasClass("lotto_slider_controller")) {
            tcg.ac.ui._changeSlideText(this, ui);
            var min_max = ui.value;
            var lottoRangeSliders = $("#main_lotto_group_sub_container").find(".range_slider_rebate");

            $.each(lottoRangeSliders, function () {
              var fval = min_max;
              if (min_max > $(this).attr("max")) {
                fval = $(this).attr("max");
              } else if (min_max < $(this).attr("min")) {
                fval = $(this).attr("min");
              }
              $(this).slider("value", fval);
              $(this).slider("enable");
              tcg.ac.ui._changeSlideText(this, { value: fval });
            });
          }
        },
      });
    });
  };

  tcg.ac.ui._createQouta_lotto = function (data) {
    var qouta_li = "<li>" + tcg.lib.prop("no_qouta") + "</li>";

    for (var i = data.length - 1; i >= 0; i--) {
      qouta_li +=
        '<li value="' +
        data[i].quotaId +
        '" data-num="' +
        i +
        '"  data-type="' +
        data[i].productType +
        '">' +
        data[i].templateName +
        "/" +
        data[i].quotaRemaining +
        "个</li>";
    }

    var str = "";
    str += '<div class="hag_register_commun hac_link_mangslc hac_link_mangschw45 lotto_dropdown_head">';
    str +=
      '<span class="hag_regcom_lt hag_reg_fc142 label" translate="ac_link_register_deadline_label">' +
      tcg.lib.prop("quota_label") +
      "</span>";
    str += '<div class="hag_regcom_nameinp hag_regcom_nameinsl custom_dropdown qouta_dropdown custom_dropdownelem">';
    str += '<input type="text" name="qutaId" readonly="readonly" placeholder=' + tcg.lib.prop("quota_label") + ">";
    str += '<div class="hag_regcom_tmslcac hide">';
    str += '<ul class="chosen-results1 hag_alluse_scroll hide">';
    str += qouta_li;
    str += "</ul>";
    str += "</div>";
    str += "</div>";
    str += "</div>";

    return str;
  };

  tcg.ac.ui._createQouta_others = function (data) {
    var qouta_li = "<li>" + tcg.lib.prop("no_qouta") + "</li>";
    for (var i = data.length - 1; i >= 0; i--) {
      qouta_li +=
        '<li value="' +
        data[i].quotaId +
        '"  data-num="' +
        i +
        '" data-type="' +
        data[i].productType +
        '">' +
        data[i].templateName +
        "/" +
        data[i].quotaRemaining +
        "个</li>";
    }

    var str = "";
    str += '<div class="hag_regcom_nameinp hag_regcom_nameinsl custom_dropdown custom_dropdownelem qouta_dropdown">';
    str += '<input type="text" name="qutaId" readonly="readonly" placeholder=' + tcg.lib.prop("quota_label") + ">";
    str += '<div class="hag_regcom_tmslcac hide">';
    str += '<ul class="chosen-results1 hag_alluse_scroll hide">';
    str += qouta_li;
    str += "</ul>";
    str += "</div>";
    str += "</div>";

    return str;
  };

  tcg.ac.ui.loadRebateDropdown = function (id, bol, _qt) {
    var slider_array = [];
    var data_html = {
      lott: "",
      rng: "",
      live: "",
      pvp: "",
      elotto: "",
      sports: "",
      fish: "",
      others: "",
      _maxrateArr: [],
      _minrateArr: [],
    };

    var lotto_qouta = "";
    if (_qt) {
      if (!$.isEmptyObject(_qt.value)) {
        if (_qt.value["4"]) {
          if (_qt.value["4"].length) {
            lotto_qouta = tcg.ac.ui._createQouta_lotto(_qt.value["4"]); //qouta html for lotto
          }
        }
      }
    }

    for (var _groups in globalVar.globeRebate) {
      var data = globalVar.series;
      var global_rebates = {};
      var gReb = globalVar.globeRebate[_groups].map(function (e) {
        global_rebates[e.gameType] = e;
      });

      for (var i = 0; i < data.length; i++) {
        var e = data[i];
        var group_code = e.type;
        var _html = "";

        if (global_rebates[group_code]) {
          var maxSeries =
            global_rebates[group_code].highestRebate < e.rebateValue
              ? global_rebates[group_code].highestRebate
              : e.rebateValue;
          maxSeries =
            maxSeries == global_rebates[group_code].minSeries
              ? maxSeries
              : maxSeries - global_rebates[group_code].rebateDifference;
          var rebate_interval =
            global_rebates[group_code].rebateInterval * 1 == 0 ? 1 : global_rebates[group_code].rebateInterval * 1;
          var minSeries = global_rebates[group_code].minSeries;
          if (minSeries > maxSeries) {
            minSeries = maxSeries;
          }
          var rebate_interval_item = maxSeries;

          var slider_ops = [];

          while (rebate_interval_item.toFixed(3) >= minSeries) {
            slider_ops.push(rebate_interval_item);
            rebate_interval_item = (rebate_interval_item * 10000 - rebate_interval * 10000) / 10000;
          }
          var _steps = slider_ops.toString();
          var odd_quota = "";

          if (_qt) {
            if (!$.isEmptyObject(_qt.value)) {
              if (_groups != 4) {
                for (var qt in _qt.value) {
                  if (_groups == qt) {
                    odd_quota = tcg.ac.ui._createQouta_others(_qt.value[qt]);
                  }
                }
              }
            }
          }
          var color_code = _groups == 4 ? "#64C814" : "rgb(197, 197, 197)";
          var rebateSeriesVal = _groups == 4 ? maxSeries : minSeries;
          _html += ' <div class="hag_register_commun hac_link_mangslc hac_link_mangschw60">';
          _html +=
            ' <span class="hag_regcom_lt hag_reg_fc142 series_label" >' +
            tcg.lib.prop("series_label_" + group_code) +
            ":</span>";
          _html +=
            ' <div class="slider_range_container hag_regcom_nameinp  custom_dropdown  custom_dropdownelem all_custom_newrebate">';
          _html +=
            ' <span style="font-size: 12px;">' +
            ((rebateSeriesVal + "").indexOf(".") > -1 ? rebateSeriesVal.toFixed(2) : rebateSeriesVal) +
            tcg.ac.rebateTransformOther(maxSeries, group_code, minSeries) +
            "</span> ";
          _html +=
            ' <div style="background:' +
            color_code +
            '"  class="range_slider_rebate" data-steps=' +
            rebate_interval +
            '  min="' +
            minSeries +
            '" max="' +
            maxSeries +
            '" data-type="' +
            group_code +
            '"  value="' +
            ((rebateSeriesVal + "").indexOf(".") > -1 ? rebateSeriesVal.toFixed(2) : rebateSeriesVal) +
            '"  data-value="' +
            ((rebateSeriesVal + "").indexOf(".") > -1 ? rebateSeriesVal.toFixed(2) : rebateSeriesVal) +
            '"  >';
          _html +=
            ' <input class="hide"  name="status" data-type="' +
            group_code +
            '"  value="' +
            ((rebateSeriesVal + "").indexOf(".") > -1 ? rebateSeriesVal.toFixed(2) : rebateSeriesVal) +
            '" >';
          _html += " </div>";
          _html += odd_quota;
          _html += " </div>";
          _html += " </div>";
          if (_groups == 4) {
            data_html._maxrateArr.push(maxSeries);
            data_html._minrateArr.push(minSeries);
            data_html.lott += _html;
            data_html.minControlValue = data_html.minControlValue ? data_html.minControlValue : rebateSeriesVal;
            data_html.minControlValue =
              rebateSeriesVal > data_html.minControlValue ? rebateSeriesVal : data_html.minControlValue;
            if (maxSeries != minSeries) {
              data_html.disableMaxControl = true;
            }
          } else {
            data_html.others += _html;
          }
        }
      }
    }

    if (data_html.lott) {
      var min_max_series = globalVar.globeRebate[4];
      var _maxrate = data_html.disableMaxControl
        ? Math.max.apply(Math, data_html._maxrateArr)
        : data_html.minControlValue;
      var _minrate = data_html.disableMaxControl ? Math.min.apply(Math, data_html._minrateArr) : _maxrate;
      var _html_lott_main = "";
      _html_lott_main += ' <div class="hag_register_commun hac_link_mangslc hac_link_mangschw60">';
      _html_lott_main += ' <span class="hag_regcom_lt hag_reg_fc142 series_label" >返点设定:</span>';
      _html_lott_main += ' <div class="slider_range_container hag_regcom_nameinp  custom_dropdown parentController">';
      _html_lott_main += ' <span style="font-size: 12px;">' + _maxrate + "</span> ";
      _html_lott_main +=
        ' <div style="background:#64c814"  class="range_slider_rebate lotto_slider_controller" data-type="SSC_1" data-steps=' +
        min_max_series[0].rebateInterval +
        ' value="' +
        _maxrate +
        '"  min="' +
        _minrate +
        '" max="' +
        _maxrate +
        '"  data-value="' +
        _maxrate +
        '"  >';
      _html_lott_main += ' <input class="hide"  name="status"  value="' + _maxrate + '" >';
      _html_lott_main += " </div>";
      _html_lott_main += " </div>";
      _html_lott_main += " </div>";
      _html_lott_main +=
        ' <div id="show_lott_btn_container"><div id="show_lott_btn"><span>+</span> 开启彩票自定义 </div></div>';
      data_html.lotto_main_slider = _html_lott_main;
    }

    var lotto_group_html = data_html.lott
      ? "<div id='lottoGroupSlider' class='rebate_lott_group'><div class='lott_box_header'><div class='lott_box_title'>" +
        tcg.lib.prop("lott_box_title") +
        "</div>" +
        "<div class='lott_min_max'><span data-mode='min' class='min_max'>" +
        tcg.lib.prop("MIN") +
        "</span><span data-mode='max' class='min_max'>" +
        tcg.lib.prop("MAX") +
        "</span>" +
        lotto_qouta +
        "</div>" +
        data_html.lotto_main_slider +
        "<div id='main_lotto_group_container' style='display:none;'>" +
        "<div id='main_lotto_group_sub_container'>" +
        data_html.lott +
        "</div>" +
        "</div>" +
        "</div>" +
        "</div>"
      : "";

    var other_group_html = data_html.others
      ? "<div class='rebate_lott_group other_group'><div class='lott_box_header'><div><div class='lott_box_title'>" +
        tcg.lib.prop("label_other") +
        "</div><div class='lott_min_max'><span data-mode='min' class='min_max'>" +
        tcg.lib.prop("MIN") +
        "</span><span data-mode='max' class='min_max'>" +
        tcg.lib.prop("MAX") +
        "</span> </div>" +
        data_html.others +
        "</div></div>"
      : "";

    $(id).html(lotto_group_html + other_group_html);

    //EVENT LISTENERS

    $(document).on("click", ".lott_min_max .min_max", function () {
      var min_max = $(this).attr("data-mode");
      var lottoRangeSliders = $(this).parents(".rebate_lott_group").find(".range_slider_rebate");
      $.each(lottoRangeSliders, function () {
        $(this).slider("value", $(this).attr(min_max));
        $(this).slider("enable");
        tcg.ac.ui._changeSlideText(this, { value: $(this).slider("option", "value") });
      });
    });

    $("#show_lott_btn").on("click", function () {
      $("#main_lotto_group_container").toggle("slide", { direction: "up" }, 500);
      if ($(this).find("span").html() == "+") {
        $(this).html("<span>-</span> 关闭彩票自定义 ");
      } else {
        $(this).html("<span>+</span> 开启彩票自定义 ");
      }
    });

    $(".rebate_lott_group").on("click", ".qouta_dropdown li", function () {
      if (!$(this).attr("data-type")) {
        if ($(this).parents(".rebate_lott_group").hasClass("other_group")) {
          var lottoRangeSliders = $(this).parents(".slider_range_container").find(".range_slider_rebate");
          $.each(lottoRangeSliders, function () {
            $(this).slider("value", $(this).attr("min"));
            $(this).slider("enable");
            tcg.ac.ui._changeSlideText(this, { value: $(this).attr("min") });
          });
        } else {
          $(this).parents(".rebate_lott_group").find(".lott_min_max .min_max[data-mode=max]").click();
        }
        return;
      }

      var data_rs = JSON.parse(sessionStorage.qt).value;
      $(this).parents(".qouta_dropdown").find("input").attr("data-value", $(this).attr("value"));
      var customerSeries = globalVar.series;
      var eme = this;
      var quotaConfig = data_rs[$(eme).attr("data-type")][$(eme).attr("data-num")];
      //var thisParent = '#deleQutarebateEache' + _let + $(eme).attr("data-type");
      for (var i = 0; i < quotaConfig.quotaRebateConfig.length; i++) {
        var quotaModeType = quotaConfig.quotaRebateConfig[i].type;
        for (var j = 0; j < customerSeries.length; j++) {
          if (customerSeries[j].type == quotaModeType) {
            var quotaModeValue =
              customerSeries[j].rebateValue > quotaConfig.quotaRebateConfig[i].rebateValue
                ? quotaConfig.quotaRebateConfig[i].rebateValue
                : customerSeries[j].rebateValue;
            var _elem = $("#deleAllQutaRebateList").find(".range_slider_rebate[data-type=" + quotaModeType + "]");
            $(_elem).slider("value", quotaModeValue);
            $(_elem).slider("disable");
            tcg.ac.ui._changeSlideText(_elem, { value: quotaModeValue });
            break;
          }
        }
      }
    });
  };

  tcg.ac.ui.loadDownlineRebateDropdown = function (id, downlineRebates, _qt, customerRebate) {
    var slider_array = [];
    var data_html = {
      lott: "",
      rng: "",
      live: "",
      pvp: "",
      elotto: "",
      sports: "",
      fish: "",
      others: "",
    };

    var lotto_qouta = "";

    if (_qt) {
      if (!$.isEmptyObject(_qt.value)) {
        if (_qt.value["4"]) {
          if (_qt.value["4"].length) {
            lotto_qouta = tcg.ac.ui._createQouta_lotto(_qt.value["4"]); //qouta html for lotto
          }
        }
      }
    }

    for (var _groups in globalVar.globeRebate) {
      var data = globalVar.series;
      var global_rebates = {};
      var downline_rebates = {};
      var gReb = globalVar.globeRebate[_groups].map(function (e) {
        global_rebates[e.gameType] = e;
      });

      var downlineRebates = customerRebate.value.configs.map(function (e) {
        var game_type = e.type;
        if (e.prizeModeId * 1 > 0) {
          game_type = e.type + "_" + e.prizeModeId;
        }
        downline_rebates[game_type] = e;
      });

      for (var i = 0; i < data.length; i++) {
        var e = data[i];
        var group_code = e.type;
        var _html = "";

        if (global_rebates[group_code]) {
          var rebate_interval_item = minSeries;

          var slider_ops = [];

          while (rebate_interval_item <= maxSeries) {
            slider_ops.push(rebate_interval_item);
            rebate_interval_item += rebate_interval;
          }
          var _steps = slider_ops.toString();

          var odd_quota = "";

          if (_qt) {
            if (!$.isEmptyObject(_qt.value)) {
              if (_groups != 4) {
                for (var qt in _qt.value) {
                  if (_groups == qt) {
                    odd_quota = tcg.ac.ui._createQouta_others(_qt.value[qt]);
                  }
                }
              }
            }
          }
          var maxSeries =
            global_rebates[group_code].highestRebate < e.rebateValue
              ? global_rebates[group_code].highestRebate
              : e.rebateValue;
          maxSeries =
            maxSeries == global_rebates[group_code].minSeries
              ? maxSeries
              : maxSeries - global_rebates[group_code].rebateDifference;
          var rebate_interval =
            global_rebates[group_code].rebateInterval * 1 == 0 ? 1 : global_rebates[group_code].rebateInterval * 1;

          var minSeries = global_rebates[group_code].minSeries;

          if (minSeries > maxSeries) {
            minSeries = maxSeries;
          }

          var current_dl_rebate = downline_rebates[group_code].rebateValue;

          _html += ' <div class="hag_register_commun hac_link_mangslc hac_link_mangschw60">';
          _html +=
            ' <span class="hag_regcom_lt hag_reg_fc142" translate="series_label_' +
            group_code +
            '">' +
            tcg.lib.prop("series_label_" + group_code) +
            ":</span>";

          _html +=
            ' <div class="slider_range_container hag_regcom_nameinp  custom_dropdown all_custom_newrebate custom_dropdownelem">';
          _html +=
            ' <span style="font-size: 12px;">' +
            ((current_dl_rebate + "").indexOf(".") > -1 ? current_dl_rebate.toFixed(2) : current_dl_rebate) +
            tcg.ac.rebateTransformOther(current_dl_rebate, group_code, current_dl_rebate) +
            "</span> ";
          _html +=
            ' <div style="background:rgb(197, 197, 197);" class="range_slider_rebate" data-steps=' +
            rebate_interval +
            '  min="' +
            current_dl_rebate +
            '" max="' +
            maxSeries +
            '" data-type="' +
            group_code +
            '"  value="' +
            ((current_dl_rebate + "").indexOf(".") > -1 ? current_dl_rebate.toFixed(2) : current_dl_rebate) +
            '"  >';
          _html +=
            ' <input class="slide_color_range hide"   name="status" data-type="' +
            group_code +
            '" value="' +
            ((current_dl_rebate + "").indexOf(".") > -1 ? current_dl_rebate.toFixed(2) : current_dl_rebate) +
            '">';
          _html += " </div>";
          _html += odd_quota;
          _html += " </div>";
          _html += " </div>";

          if (_groups == 4) {
            data_html.lott += _html;
          } else {
            data_html.others += _html;
          }
        }
      }
    }

    var lotto_group_html = data_html.lott
      ? "<div id='lottoGroupSlider' class='rebate_lott_group'><div class='lott_box_header'><div class='lott_box_title'>" +
        tcg.lib.prop("lott_box_title") +
        "</div>" +
        "<div class='lott_min_max'><span data-mode='min' class='min_max'>" +
        tcg.lib.prop("MIN") +
        "</span><span data-mode='max' class='min_max'>" +
        tcg.lib.prop("MAX") +
        "</span>" +
        lotto_qouta +
        "</div>" +
        data_html.lott +
        "</div></div>"
      : "";

    var other_group_html = data_html.others
      ? "<div class='rebate_lott_group other_group'><div class='lott_box_header'><div><div class='lott_box_title'>" +
        tcg.lib.prop("label_other") +
        "</div><div class='lott_min_max'><span data-mode='min' class='min_max'>" +
        tcg.lib.prop("MIN") +
        "</span><span data-mode='max' class='min_max'>" +
        tcg.lib.prop("MAX") +
        "</span> </div>" +
        data_html.others +
        "</div></div>"
      : "";
    $(id).html(lotto_group_html + other_group_html);

    //EVENT LISTENERS

    $(document).on("click", ".lott_min_max .min_max", function () {
      var min_max = $(this).attr("data-mode");
      var lottoRangeSliders = $(this).parents(".rebate_lott_group").find(".range_slider_rebate");
      $.each(lottoRangeSliders, function () {
        $(this).slider("value", $(this).attr(min_max));
        $(this).slider("enable");
        tcg.ac.ui._changeSlideText(this, { value: $(this).slider("option", "value") });
      });
    });

    $(".rebate_lott_group").on("click", ".qouta_dropdown li", function () {
      if (!$(this).attr("data-type")) {
        if ($(this).parents(".rebate_lott_group").hasClass("other_group")) {
          var lottoRangeSliders = $(this).parents(".slider_range_container").find(".range_slider_rebate");
          $.each(lottoRangeSliders, function () {
            $(this).slider("value", $(this).attr("min"));
            $(this).slider("enable");
            tcg.ac.ui._changeSlideText(this, { value: $(this).attr("min") });
          });
        } else {
          $(this).parents(".rebate_lott_group").find(".lott_min_max .min_max[data-mode=max]").click();
        }
        return;
      }

      var data_rs = JSON.parse(sessionStorage.qt).value;
      $(this).parents(".qouta_dropdown").find("input").attr("data-value", $(this).attr("value"));
      var customerSeries = globalVar.series;
      var eme = this;
      var quotaConfig = data_rs[$(eme).attr("data-type")][$(eme).attr("data-num")];
      //var thisParent = '#deleQutarebateEache' + _let + $(eme).attr("data-type");
      for (var i = 0; i < quotaConfig.quotaRebateConfig.length; i++) {
        var quotaModeType = quotaConfig.quotaRebateConfig[i].type;
        for (var j = 0; j < customerSeries.length; j++) {
          if (customerSeries[j].type == quotaModeType) {
            var quotaModeValue =
              customerSeries[j].rebateValue > quotaConfig.quotaRebateConfig[i].rebateValue
                ? quotaConfig.quotaRebateConfig[i].rebateValue
                : customerSeries[j].rebateValue;
            var _elem = $("#member_rebates_dropdown").find(".range_slider_rebate[data-type=" + quotaModeType + "]");
            $(_elem).slider("value", quotaModeValue);
            $(_elem).slider("disable");
            tcg.ac.ui._changeSlideText(_elem, { value: quotaModeValue });
            break;
          }
        }
      }
    });
  };
  tcg.ac.ui.zeroClipboardInit = function (elem) {
    //  Copy url
    // ZeroClipboard.config( { moviePath: window.location.origin + "http://apps.bdimg.com/libs/zeroclipboard/2.2.0/ZeroClipboard.swf" } );
    // var clip = new ZeroClipboard($(".zeroclipboard"));
    // clip.on('complete', function(client,args){
    //     tcg.lib.alert("success", (tcg.lib.prop("label_success_copy")));
    // });
    $("[data-clipboard], .zeroclipboard")
      .unbind("click")
      .bind("click", function () {
        //var link = $(this).find("span:last-child").html();// + "/register.html";
        var link = $(this).attr("data-clipboard-text");
        var input = document.createElement("INPUT");
        input.setAttribute("type", "text");
        input.setAttribute("name", "affiliateUrl");
        input.setAttribute("value", link);
        input.setAttribute("style", "position: absolute; z-index: -9999");
        $(this).append(input);
        $("[name='affiliateUrl']").select();
        try {
          var successful = document.execCommand("copy");
          var msg = successful ? "label_success_copy" : "linkManager_success_fail";
          tcg.lib.alert("success", tcg.lib.prop(msg), null, function () {
            $("[name='affiliateUrl']").remove();
          });
        } catch (err) {}
      });
  };
  tcg.ac.ui.copyClipboard = function (elem) {
    // var e = elem || $("[data-clipboard], .zeroclipboard");
    // ZeroClipboard.setDefaults({
    // 	moviePath: "http://apps.bdimg.com/libs/zeroclipboard/2.2.0/ZeroClipboard.swf"
    // });
    // var clip = new ZeroClipboard(e);

    // return clip;

    $("[data-clipboard], .zeroclipboard")
      .unbind("click")
      .bind("click", function () {
        var link = $(this).find("span:last-child").html(); // + "/register.html";

        var input = document.createElement("INPUT");
        input.setAttribute("type", "text");
        input.setAttribute("name", "affiliateUrl");
        input.setAttribute("value", link);
        input.setAttribute("style", "position: absolute; z-index: -9999");
        $(this).append(input);
        $("[name='affiliateUrl']").select();
        try {
          var successful = document.execCommand("copy");
          var msg = successful ? "label_success_copy" : "linkManager_success_fail";
          tcg.lib.alert("success", tcg.lib.prop(msg), null, function () {
            $("[name='affiliateUrl']").remove();
          });
        } catch (err) {}
      });
  };
  tcg.ac.ui.cleanGameSeries = function (cleanGameSeries, _html) {
    _html += "<ul class='dira-regdline-gb-list'>";
    //gameSeries.forEach(function(entry,index) {
    $.each(cleanGameSeries, function (index, entry) {
      var gameCode = entry.gameGroupCode + entry.prizeModeId;
      _html += "<li class='dira-regdline-gb-listitem'>";
      _html +=
        "<input type='checkbox' class='mg-check' id='check" +
        index +
        "' name='lottery' value=" +
        entry.gameGroupCode +
        " data-gameCode='" +
        gameCode +
        "' checked />";
      _html += "<label for='check" + index + "'>" + tcg.lib.prop(entry.gameGroupCode + entry.prizeModeId) + ":</label>";
      _html += "<div class='quota' id='lottQuota" + index + "'></div>";
      _html += "<input type='text' class='quota-amount' gameCode='" + gameCode + "' placeholder='-----' />";
      _html += "<div class='minusBtn raeBtn'>";
      _html += "<input type='button' class='minus'/>";
      _html += "</div>";
      _html += "<div class='plusBtn raeBtn'>";
      _html += "<input type='button' class='plus'/>";
      _html += "</div>";
      _html += "</li>";
    });
    _html += "</ul>";
    return _html;
  };
  tcg.ac.ui.sscGamesSeriesSwitch = function () {
    $(".dira-regdline-gb-listitem input[data-gameCode='SSC1']").change(function () {
      var isChecked = $(this).is(":checked");
      if (!isChecked) {
        if ($(".dira-regdline-gb-listitem input[data-gameCode='SSC2']").is(":checked")) {
          $(".dira-regdline-gb-listitem input[data-gameCode='SSC2']").trigger("click");
          $(".dira-regdline-gb-listitem input[data-gameCode='SSC2']").attr("disabled", true);
        } else {
          $(".dira-regdline-gb-listitem input[data-gameCode='SSC2']").attr("disabled", true);
        }
      } else {
        if (!$(".dira-regdline-gb-listitem input[data-gameCode='SSC2']").is(":checked")) {
          $(".dira-regdline-gb-listitem input[data-gameCode='SSC2']").removeAttr("disabled");
          $(".dira-regdline-gb-listitem input[data-gameCode='SSC2']").trigger("click");
        }
      }
    });
  };
  tcg.ac.ui.clickQuotaTab = function (id) {
    $(document)
      .off("click", id + " [type='radio']")
      .on("click", id + " [type='radio']", function () {
        var quotaId = $(this).attr("quota-id");
        var rel = $(this).attr("data-rel");
        var radio_btn = this;
        var quotaRebateConfig = null;

        $.each(globalVar.quotaObj, function (index, entry) {
          if (entry.quotaId == quotaId) {
            quotaRebateConfig = entry.quotaRebateConfig;
          }
        });
        $("#gameSeries input[type=text]").each(function () {
          for (var i = 0; i < quotaRebateConfig.length; i++) {
            var gameCode = quotaRebateConfig[i].type + quotaRebateConfig[i].prizeModeId;
            var inputCode = $(this).attr("gameCode");
            if (inputCode == gameCode) {
              $(this).val(quotaRebateConfig[i].rebateValue);
              var adj_cbox = $("input[value='" + quotaRebateConfig[i].type + "']", $(this).parent());
              if (!$(adj_cbox).is(":checked")) {
                if ($(adj_cbox).is(":disabled")) {
                  $(adj_cbox).removeAttr("disabled").trigger("click").attr("disabled", "disabled");
                } else {
                  $(adj_cbox).trigger("click");
                }
              }

              $(".quota").slider({ value: quotaRebateConfig[i].rebateValue });
            }
          }
        });
        $(".quota-amount").each(function () {
          var maximum = $(this).val() * 1;
          var temp_gameCode = $(this).attr("gameCode");
          maximum =
            maximum < globalVar.customerSeries[temp_gameCode]["maxSeries"]
              ? maximum
              : globalVar.customerSeries[temp_gameCode]["maxSeries"];
          $(this).val(maximum);
          $(this).siblings(".quota").slider("option", {
            value: maximum,
            max: maximum,
          });
        });
        $("#rebateQuota input[type=radio]").each(function (index, elem) {
          $(radio_btn).attr("quota-id") != $(elem).attr("quota-id")
            ? $(elem).removeClass("mg-radio-btn")
            : $(radio_btn).addClass("mg-radio-btn");
        });
      });
  };
  tcg.ac.ui.rQuotaSwitch = function (form, tab, gameRebates) {
    var rQuotas = $(tab + " li input[type='radio'], " + tab + " li label"),
      rRebates = $(
        gameRebates +
          " li input[type='checkbox'], " +
          gameRebates +
          " li .quota-amount, " +
          gameRebates +
          " li div .minus, " +
          gameRebates +
          " li div .plus"
      );
    if ($(form + " #rQuotaSwitch").is(":checked")) {
      rQuotas.removeAttr("disabled");
      rQuotas.removeClass("disabled");
      rRebates.attr("disabled", "").closest("li").addClass("disable");
      $(".quota").slider("option", "disabled", true);
      $(form + " #rQuotaSwitch").addClass("mg-check");
      $("#gameSeries input[type='checkbox']").each(function () {
        $(this).addClass("mg-check").prop("checked", true);
      });
      $(".quota-amount[gameCode='SSC2']").removeAttr("disabled");
      $(".quota-amount[gameCode='SSC2']").siblings("input").removeAttr("disabled");
      $(".quota-amount[gameCode='SSC2']").siblings(".quota").slider("option", "disabled", false);
    } else {
      rQuotas.attr("disabled", "");
      rQuotas.addClass("disabled");
      rQuotas.removeAttr("checked");
      $(form + " .mg-radio-btn").removeClass("mg-radio-btn");
      rRebates.removeAttr("disabled").closest("li").removeClass("disable");
      $(".quota").slider("option", "disabled", false);

      $("#gameSeries input[type=text]").each(function () {
        var minseries = $(this).attr("min") || 0;
        $(this).val(minseries);
        $(".quota").slider({ value: minseries });
      });
      $(form + " #rQuotaSwitch").removeClass("mg-check");
      $(".quota-amount").each(function () {
        $(this).val($(this).attr("min"));
        $(this)
          .siblings(".quota")
          .slider("option", {
            value: $(this).attr("min"),
            max: $(this).attr("max"),
          });
      });
    }
  };
  tcg.ac.ui.onGamesSeriesBoxClick = function (form, queryDown) {
    $(document)
      .off("click", form + " input[name='lottery']")
      .on("click", form + " input[name='lottery']", function () {
        if (queryDown) {
          return false;
        }
        if ($(this).prop("checked")) {
          var minSeries = $(this).siblings("input[type='text']").attr("min") || 0;
          $(this).siblings("input[type='text']").val(minSeries).attr("readonly", false);
          $(this).siblings(".quota").slider("value", minSeries);
          $(this).siblings(".quota").slider("enable");
          $(this).siblings(".quota-amount").removeAttr("disabled");
          $(this).siblings(".minusBtn").find(".minus").removeAttr("disabled");
          $(this).siblings(".plusBtn").find(".plus").removeAttr("disabled");
          $(this).addClass("mg-check");
        } else {
          var minSeries = $(this).siblings("input[type='text']").attr("min");
          $(this).siblings("input[type='text']").val(minSeries).attr("readonly", true);
          //$(this).siblings(".quota").slider("value",minSeries);
          $(this).siblings(".quota").slider("disable");
          $(this).siblings(".quota-amount").attr("disabled", "true");
          $(this).siblings(".minusBtn").find(".minus").attr("disabled", "true");
          $(this).siblings(".plusBtn").find(".plus").attr("disabled", "true");
          $(this).removeClass("mg-check");
        }
      });
  };
  tcg.ac.ui.sliderAmount = function (value, min, max, step, index, f, rand) {
    var form = $(f),
      lotteries = form.find("input[name='lottery']"),
      ranMin,
      last = 0;
    if (rand === true) {
      ranMin = Math.floor(Math.random() * (max - min + 1)) + min;
    } else {
      ranMin = min;
    }

    var globeRebate_map = {};
    $(globalVar.globeRebate).map(function () {
      switch (this.gameType) {
        case "SSC_1":
          globeRebate_map["SSC1"] = this.highestRebate * 1;
          break;
        case "SSC_2":
          globeRebate_map["SSC2"] = this.highestRebate * 1;
          break;
        case "11X5_1":
          globeRebate_map["11X5"] = this.highestRebate * 1;
          break;
        case "LF_1":
          globeRebate_map["FC3D"] = this.highestRebate * 1;
          globeRebate_map["TCP3P5"] = this.highestRebate * 1;
          break;
        case "PK10_1":
          globeRebate_map["PK10"] = this.highestRebate * 1;
          break;
      }
    });
    lotteries.eq(index).siblings(".quota-amount").val(ranMin);
    lotteries.eq(index).siblings(".quota-amount").attr("max", max);
    lotteries.eq(index).siblings(".quota-amount").attr("min", min);
    lotteries.eq(index).siblings(".plusBtn").find(".plus").val(" ");
    lotteries.eq(index).siblings(".minusBtn").find(".minus").val(" ");
    lotteries
      .eq(index)
      .siblings(".quota")
      .slider({
        value: ranMin,
        orientation: "horizontal",
        range: "min",
        min: min,
        max: max,
        animate: true,
        step: step,
        slide: function (event, ui) {
          lotteries.eq(index).siblings(".quota").siblings("input.quota-amount").val(ui.value);
          var gameCode = lotteries.eq(index).siblings("input.quota-amount").attr("gameCode");
          if (ui.value >= max || ui.value >= globeRebate_map[gameCode]) {
            lotteries.eq(index).siblings(".quota").removeClass("sa-blue");
            lotteries.eq(index).siblings(".quota").addClass("sa-red");
          } else {
            lotteries.eq(index).siblings(".quota").removeClass("sa-red");
            lotteries.eq(index).siblings(".quota").addClass("sa-blue");
          }
        },
        change: function (event, ui) {
          var gameCode = lotteries.eq(index).siblings("input.quota-amount").attr("gameCode");
          if (ui.value >= max || ui.value >= globeRebate_map[gameCode]) {
            lotteries.eq(index).siblings(".quota").removeClass("sa-blue");
            lotteries.eq(index).siblings(".quota").addClass("sa-red");
          } else {
            lotteries.eq(index).siblings(".quota").removeClass("sa-red");
            lotteries.eq(index).siblings(".quota").addClass("sa-blue");
          }
        },
      });

    lotteries
      .eq(index)
      .siblings(".quota-amount")
      .on("change", function () {
        var newValue = parseInt($(this).val());

        if (newValue > max) {
          $(this).prevAll(".quota").slider({
            value: max,
          });

          $(this).val(max);
        } else if (newValue < min) {
          $(this).prevAll(".quota").slider({
            value: min,
          });
          $(this).val(min);
        } else {
          $(this).prevAll(".quota").slider({
            value: newValue,
          });

          $(this).val(newValue);
        }
        if (isNaN(newValue)) {
          $(this).val(min);
        }
      });
    //
    lotteries
      .eq(index)
      .siblings(".plusBtn")
      .children(".plus")
      .click(function () {
        var thisQuota = $(this).parent().prevAll(".quota"),
          value = thisQuota.slider("value"),
          max = thisQuota.slider("option", "max");
        nextAmount = parseInt($(this).parent().prevAll(".quota-amount").val());
        step = thisQuota.slider("option", "step");

        thisQuota.slider("value", value + step);

        if (nextAmount >= max) {
          $(this).parent().prevAll(".quota-amount").val(value);
        } else {
          $(this)
            .parent()
            .prevAll(".quota-amount")
            .val(value + step);
        }
      });

    lotteries
      .eq(index)
      .siblings(".minusBtn")
      .children(".minus")
      .click(function () {
        var thisQuota = $(this).parent().prevAll(".quota"),
          value = thisQuota.slider("value"),
          min = thisQuota.slider("option", "min");
        nextAmount = parseInt($(this).parent().prevAll(".quota-amount").val());
        step = thisQuota.slider("option", "step");

        thisQuota.slider("value", value - step);

        if (nextAmount <= min) {
          $(this).parent().prevAll(".quota-amount").val(value);
        } else {
          $(this)
            .parent()
            .prevAll(".quota-amount")
            .val(value - step);
        }
      });
  };

  tcg.ac.ui.agentRedRain = function (r, c) {
    var _html = "";
    $("#rpt_search_custmore").empty();
    $.each(r, function (k, v) {
      if (!c[k]) {
        _html +=
          '<div class="downline"><input type="checkbox" name="cb" id="' +
          v +
          '" data-value=' +
          v +
          ' data-name="' +
          k +
          '"><label for="' +
          v +
          '">' +
          k +
          "</label></div>";
      } else {
        _html +=
          '<div class="downline"><input type="checkbox" name="cb" checked id="' +
          v +
          '" data-value=' +
          v +
          ' data-name="' +
          k +
          '"><label for="' +
          v +
          '">' +
          k +
          "</label></div>";
      }
    });
    // _html = _html=="" ? '<div>暂无数据</div>':_html;

    $("#rpt_search_custmore").html(_html);
    tcg.ac.agentRedRainClick();
  };
  tcg.ac.ui.agentRedRainSelect = function (r) {
    var _html = "";
    if (r.length > 0) {
      $.each(r, function (k, v) {
        if (k != "length") {
          _html +=
            '<div class="dynm-rpt rd" data-value=' +
            v +
            ' data-name="' +
            k +
            '">' +
            k +
            '<div class="close-rpf"></div></div>';
        }
      });
    } else {
      _html = '<div class="dynm-rpt">' + tcg.lib.prop("label_input_receiver") + "</div>";
    }
    $("#select_send_redrain").html(_html);
  };

  tcg.ac.ui.registerDownlineQuota = function (msg, userType, username, password, lotteries, encodeValue) {
    // Declare
    var l = [];
    encodeValue["rebateSettingConfigs"] = [];

    msg += "<div style='text-align:left; margin-left:40px; color:#fff;'>";
    msg +=
      tcg.lib.prop("ac_bet_content_1") +
      ":" +
      (userType == 1 ? tcg.lib.prop("label_agent") : tcg.lib.prop("label_member")) +
      "<br/>";
    msg += tcg.lib.prop("label_registration_account") + ":" + username + "<br/>";
    msg +=
      tcg.lib.prop("label_pwd_login") +
      ":" +
      (password != "" ? password : tcg.lib.prop("ac_default_password")) +
      "<br/>";

    for (var i = 0; i < lotteries.length; i++) {
      var gameGroup = lotteries.eq(i).siblings("input.quota-amount").attr("gamecode");
      var groupCode = gameGroup.substr(gameGroup.length - 1);
      //encodeValue[i+4] = lotteries.eq(i).val() + ","+groupCode+"," + lotteries.eq(i).siblings("input.quota-amount").val();
      l[i] = {
        type: lotteries.eq(i).val() + "_" + groupCode,
        rebateValue: lotteries.eq(i).siblings("input.quota-amount").val(),
      };
      encodeValue["rebateSettingConfigs"].push(l[i]);
      msg +=
        lotteries.eq(i).siblings("label").text().replace(":", "") +
        tcg.lib.prop("ac_bonus_group") +
        ":" +
        lotteries.eq(i).siblings("input.quota-amount").val() +
        "<br/>";
    }
    msg += "</div>";
    return msg;
  };

  tcg.ac.ui.chosePathType = function () {
    $(document)
      .off("change", "#generateAffiliateUrlForm select[name='pathType']")
      .on("change", "#generateAffiliateUrlForm select[name='pathType']", function () {
        var value = $(this).children("option:selected").val();
        if (value == 0 || value == 1) {
          $("#generateAffiliateUrlForm input[name='dira-genaffurl-promotionpath']").val(
            $(this).children("option:selected").text()
          );
          $("#generateAffiliateUrlForm input[name='dira-genaffurl-promotionpath']").attr("readonly", true);
        } else if (value == 2) {
          $("#generateAffiliateUrlForm input[name='dira-genaffurl-promotionpath']").val("");
          $("#generateAffiliateUrlForm input[name='dira-genaffurl-promotionpath']").attr("placeholder", "--");
          $("#generateAffiliateUrlForm input[name='dira-genaffurl-promotionpath']").attr("readonly", false);
        }
      });

    $("#generateAffiliateUrlForm select[name='pathType']").trigger("change");
  };
  tcg.ac.ui.submitGenerateAffiliateUrl = function () {
    $(document)
      .off("click", "#generateAffiliateUrlForm .form-submit")
      .on("click", "#generateAffiliateUrlForm .form-submit", function () {
        tcg.ac.generateAffiliateUrl();
      });
  };

  tcg.ac.ui.shortenPathName = function (channelName) {
    var cn = channelName;
    if (cn.length > 4) {
      cn = cn.substring(0, 4) + "...";
      cn += '<div class="pop-yel-2"><span>' + channelName + "</span></div>";
    }
    return cn;
  };
  tcg.ac.ui.beautifyAffiliateUrl = function (code, url_back) {
    var data = tcg.ac.ui.generateAffiliateUrl(code);
    var _text = data.hostname + url_back;
    var _template1 =
      "<div class='hover-tips'><span class='ellipsis'>{0}</span><div class='content-tips {2}' {4}>{1}{3}</div></div>";
    return _text && _text.byte() > 11 ? _template1.format(_text, _text) : _text;
  };
  tcg.ac.ui.loadAgentDownlines = function (result) {
    //  Declare
    var _html = "",
      registerAmount = 0;
    var TYPE = [tcg.lib.prop("label_member"), tcg.lib.prop("label_agent")],
      STATUS = [
        tcg.lib.prop("ac_link_manager_status_option_4"),
        tcg.lib.prop("ac_link_manager_status_option_2"),
        tcg.lib.prop("ac_link_manager_status_option_3"),
      ];
    //  Show result

    var append_static_url = 0;

    if (sessionStorage.AGECEN) {
      var AGCEN = JSON.parse(sessionStorage.AGECEN);
      for (var i = AGCEN.childrenNodes.length - 1; i >= 0; i--) {
        if (AGCEN.childrenNodes[i].modId == "REGCEN") {
          for (var j = AGCEN.childrenNodes[i].childrenNodes.length - 1; j >= 0; j--) {
            if (AGCEN.childrenNodes[i].childrenNodes[j].modId == "AFFURLEXT3") {
              append_static_url = AGCEN.childrenNodes[i].childrenNodes[j].enabled;
            }
          }
        }
      }
    }

    if (result.List.length > 0) {
      for (var i = 0; i < result.List.length; i++) {
        var entry = result.List[i];

        registerAmount += entry.affiliateCount;
        var merchantC = window.sessionStorage.getItem("merchantCode");
        var data = tcg.ac.ui.generateAffiliateUrl(entry.code),
          url_back = tcg.ac.mechantConfig(merchantC);

        var append_register = append_static_url == "1" ? "/register.html" : "";

        _html += "<div class='divTableRow border-bot clearfix'>";
        _html +=
          "<div class='divTableCell yel-con-2'>" +
          (!entry.path ? "系统生成" : entry.path == " " ? "系统生成" : entry.path) +
          "</div>";
        _html +=
          "<div class='divTableCell div-x tbl-link ico-file zeroclipboard' data-clipboard-text='" +
          data.url +
          url_back +
          append_register +
          "'><span class='icocss'></span><span class='icocss'></span>" +
          tcg.ac.ui.beautifyAffiliateUrl(entry.code, url_back + append_register) +
          "</div>";
        _html += "<div class='divTableCell div-z registerCount'>" + entry.affiliateCount + "</div>";
        /* Disable click event, removed classes (tbl-link registerCount) */
        _html += "<div class='divTableCell div-y'>" + TYPE[entry.type] + "</div>";
        _html += "<div class='divTableCell div-y'>" + STATUS[entry.status] + "</div>";
        _html += "<div class='divTableCell'>" + entry.startDate + "</div>";
        _html +=
          "<div class='divTableCell'>" +
          (entry.endDate != null ? entry.endDate : tcg.lib.prop("ac_link_register_deadline_option_1")) +
          "</div>";
        _html += "<div class='divTableCell div-y'>";
        _html += "<div class='tbl-gear game-icons'>";
        _html += "<ul class='dropdown-opts hide'>";
        _html += "<span class='arrow-up'></span>";
        _html += "<li class='affiliateUrlDetail'>" + tcg.lib.prop("label_details") + "</li>";
        _html += "<li class='affiliateUrlDelete'>" + tcg.lib.prop("ac_delete_btn") + "</li>";
        _html += "</ul>";
        _html += "</div>";
        _html += "</div>";
        _html += "<div style='display:none;' class='entry'>" + JSON.stringify(entry) + "</div>";
        _html += "</div>";
      }
    } else {
      _html += "<div class='tableContent-wrp'><div class='noResult-data'>" + tcg.lib.prop("no_result") + "</div>";
    }

    $("#linkManagerList").html(_html);
    $("#totalEffectiveAmount").text(registerAmount);

    //  Copy url
    tcg.ac.ui.zeroClipboardInit($(".zeroclipboard"));
  };
  tcg.ac.ui.generateAffiliateUrl = function (code) {
    var url = "";
    var hostname = "";
    var baseUrl = window.location.host.split(".");

    hostname = code + "." + baseUrl[baseUrl.length - 2] + "." + baseUrl[baseUrl.length - 1];
    url += window.location.protocol + "//";
    url += hostname;
    //url += window.location.port == "80" ? "/" : ":" + window.location.port + "/";
    // url += "/register.html";
    return { hostname: hostname, url: url };
  };

  tcg.ac.ui.appendBreadCrumbs = function (data) {
    var _html = "";
    for (var i = 0; data.length > i; i++) {
      _html +=
        "<div class='arrow-small-con mem-icon2 inline-block b-list new-entry' data-customerId='" +
        data[i].customerName +
        "'>" +
        data[i].customerName +
        "</div>";
    }
    $("#breadcrumbs").html(_html);
  };

  tcg.ac.ui.searchMemberManagement = function () {
    $(document)
      .off("click", "#memberManagementForm .form-submit")
      .on("click", "#memberManagementForm .form-submit", function () {
        $("#breadcrumbs .b-list.new-entry").remove();
        tcg.ac.getMemberManagement(true);
      });
  };
  tcg.ac.ui.searchMemberManagementV2 = function () {
    $(document)
      .off("click", "#memberManagementForm .form-submit")
      .on("click", "#memberManagementForm .form-submit", function (e) {
        if (e.originalEvent !== undefined) {
          $("#breadcrumbs .b-list.new-entry").remove();
          $("#memberManagementForm input[name='pageNo']").val(1);
          tcg.ac.getMemberManagementV2();
        } else {
          var len = $("#memberManagement #breadcrumbs .b-list").length - 1;
          var customerId = $($("#memberManagement #breadcrumbs .b-list")[len]).attr("data-customerid");
          if ($(".divTableCell.mm-th4.onel-th.div-x.sort").hasClass("asc")) {
            var type = $(".divTableCell.mm-th4.onel-th.div-x.sort").attr("data-type");
            tcg.ac.getMemberManagementV2(customerId, type, "asc");
          } else if ($(".divTableCell.mm-th4.onel-th.div-x.sort").hasClass("desc")) {
            var type = $(".divTableCell.mm-th4.onel-th.div-x.sort").attr("data-type");
            tcg.ac.getMemberManagementV2(customerId, type, "desc");
          } else {
            tcg.ac.getMemberManagementV2(customerId);
          }
        }
      });
  };
  tcg.ac.ui.clickMemberManagementBreadcrumbs = function () {
    $(document)
      .off("click", "#breadcrumbs .b-list")
      .on("click", "#breadcrumbs .b-list", function () {
        var user_customerId = `${tcg.ac.customerId}`;
        var customerId = $(this).attr("data-customerId"),
          removeFromList = false;
        $("#breadcrumbs .b-list").each(function () {
          if (removeFromList) $(this).remove();
          if ($(this).attr("data-customerId") == customerId) {
            removeFromList = true;
          }
        });
        if (user_customerId == customerId) {
          tcg.ac.getMemberManagement(true);
        } else {
          tcg.ac.getMemberManagement(true, customerId);
        }
      });
  };
  tcg.ac.ui.clickMemberManagementBreadcrumbsV2 = function () {
    $(document)
      .off("click", "#breadcrumbs .b-list")
      .on("click", "#breadcrumbs .b-list", function () {
        var username = window.sessionStorage.getItem("username");
        var customerId = $(this).attr("data-customerId"),
          removeFromList = false;
        $("#breadcrumbs .b-list").each(function () {
          if (removeFromList) $(this).remove();
          if ($(this).attr("data-customerId") == customerId) {
            removeFromList = true;
          }
        });
        $("#memberManagementForm input[name='pageNo']").val(1);
        $(".divTableCell.mm-th4.onel-th.div-x.sort").removeClass("asc");
        $(".divTableCell.mm-th4.onel-th.div-x.sort").removeClass("desc");
        tcg.ac.getMemberManagementV2(customerId);
      });
  };
  tcg.ac.ui.loadMemberManagement = function (result, isTransferEnabled) {
    var memberList = result;
    var list = "",
      decimal = 2;
    if (memberList != null) {
      for (var i = 0; i < memberList.length; i++) {
        // $.each(memberList, function(index, entry) {
        var lastLoginTime = memberList[i].last_login_time == null ? new Date() : memberList[i].last_login_time.time;
        var registerDate = memberList[i].reg_date == null ? new Date() : memberList[i].reg_date.time;
        var status =
          memberList[i].activeFlag == 0
            ? tcg.lib.prop("ac_active_flag")
            : memberList[i].activeFlag == 1
            ? tcg.lib.prop("ac_link_manager_status_option_2")
            : memberList[i].activeFlag == 2
            ? tcg.lib.prop("ac_active_flag")
            : memberList[i].activeFlag == 5
            ? tcg.lib.prop("ac_active_flag")
            : memberList[i].activeFlag == 6
            ? tcg.lib.prop("ac_active_flag")
            : memberList[i].activeFlag == 7
            ? tcg.lib.prop("ac_active_flag_7")
            : "N/A";
        var super6Rebate = memberList[i].super6Rebate == 0 ? "-" : memberList[i].super6_rebate;
        var sscRebate = memberList[i].sscRebate == 0 ? "-" : memberList[i].ssc_rebate;
        var accountBal = tcg.ac.customCurrencyFormat(memberList[i].avail_balance, decimal);
        if (i % 2 == 0) {
          list += '<div class="divTableRow border-bot clearfix">';
        } else {
          list += '<div class="divTableRow border-bot clearfix divTableRowothr">';
        }
        list +=
          '<div class="divTableCell div-x tbl-link showdetail" data-downline=' +
          memberList[i].customer_id +
          ">" +
          memberList[i].customer_name +
          "</div>";
        list +=
          '<div class="divTableCell ac_memberType">' +
          (memberList[i].type == 1 ? tcg.lib.prop("label_agent") : tcg.lib.prop("label_member")) +
          "</div>";
        list += '<div class="divTableCell ac_teamSize">' + memberList[i].team_size + "</div>";
        list +=
          '<div class="divTableCell div-z ac_registerDate">' +
          tcg.ac.formatDateFull(registerDate * 1, "yyyy-MM-dd hh:mm") +
          "</div>";
        list +=
          '<div class="divTableCell div-x ac_inactiveDays">' +
          tcg.ac.days_between(new Date(), new Date(lastLoginTime * 1)) +
          "</div>";
        list += '<div class="divTableCell ac_sscRebate">' + sscRebate + "</div>";
        list += '<div class="divTableCell ac_liveRebate">' + memberList[i].live_rebate + "</div>";
        list += '<div class="divTableCell ac_rngRebate">' + memberList[i].rng_rebate + "</div>";
        list += '<div class="divTableCell ac_pvpRebate">' + memberList[i].pvp_rebate_new + "</div>";
        // list += '<div class="divTableCell ac_super6Rebate">'+ super6Rebate +'</div>';
        // list += '<div class="divTableCell ">'+ status  +'</div>';
        list +=
          '<div class="divTableCell div-x ps-num ac_availableBalance" data-switchDecimal="' +
          decimal +
          '"  data-value="' +
          memberList[i].avail_balance.toFixed(2) +
          '">' +
          accountBal +
          "</div>";
        list +=
          '<div class="divTableCell div-y"><div class="tbl-gear game-icons">' +
          '<ul class="dropdown-opts hide"><span class="arrow-up"></span>';
        if ($("#breadcrumbs div").length == 1) {
          list +=
            '<li class="setRebate" data-customer_id="' +
            memberList[i].customer_id +
            '" data-customer_name="' +
            memberList[i].customer_name +
            '">' +
            tcg.lib.prop("ac_rebate_set") +
            "</li>";
        }
        if ($("#breadcrumbs div").length == 1 && memberList[i].type == 1) {
          list += '<li class="TransferQuota">' + tcg.lib.prop("ac_transfer_quota") + "</li>";
        }
        list +=
          '<li class="bettingHistoryLink" data-customer_name="' +
          memberList[i].customer_name +
          '">' +
          tcg.lib.prop("ac_game_record") +
          "</li>";
        if (isTransferEnabled == 1 && $("#breadcrumbs div").length == 1) {
          list +=
            '<li class="transferToDown" data-downline="' +
            memberList[i].customer_name +
            '">' +
            tcg.lib.prop("ac_transfer_to_downline") +
            "</li>";
        }
        if (memberList[i].type == 0 && $("#breadcrumbs div").length == 1) {
          list += '<li class="setAgent">' + tcg.lib.prop("ac_set_agent") + "</li>";
        }
        if (globalVar.cid == memberList[i].recommender_id) {
          list +=
            '<li class="sendMessage" data-customerName="' +
            memberList[i].customer_name +
            '" data-customerId="' +
            memberList[i].customer_id +
            '" >' +
            tcg.lib.prop("ac_sent_a_message") +
            "</li>";
        }
        list += "</ul></div></div>";
        list += '<div class="entry" style="display:none;">' + JSON.stringify(memberList[i]) + "</div>";
        list += "</div>";
        // });
      }
    }
    // list = '';
    $("#memberManagementList").html(list);

    // switch(tcg.ac.getMerchant()){
    // 	case "500cai":
    // 		// TCG-6214
    // 		$(".mm-th7, .ac_super6Rebate").remove();
    // 	break;
    // 	default:
    // }

    $("#totalTeamSize").html(sessionStorage.teamSize);
    $("#totalPlannedAmount").html(tcg.ac.customCurrencyFormat(sessionStorage.teamBalance, decimal)).attr({
      "data-SwitchDecimal": decimal,
      "data-value": sessionStorage.teamBalance,
    });
  };
  tcg.ac.ui.loadMemberManagementV2 = function (result, isTransferEnabled, footer) {
    var memberList = result;
    var list = "",
      decimal = 2,
      teamBalance = 0,
      teamSize = 0,
      downlineClass = "";
    if (memberList != null) {
      for (var i = 0; i < memberList.length; i++) {
        // $.each(memberList, function(index, entry) {
        var lastLoginTime =
          memberList[i].lastLoginTimeInTimestamp == null
            ? "--"
            : moment(memberList[i].lastLoginTimeInTimestamp).format("YYYY-MM-DD  HH:mm:ss");
        var registerDate =
          memberList[i].regDateInTimestamp == null
            ? moment().format("YYYY-MM-DD  HH:mm:ss")
            : moment(memberList[i].regDateInTimestamp).format("YYYY-MM-DD  HH:mm:ss");
        var status =
          memberList[i].activeFlag == 0
            ? tcg.lib.prop("ac_active_flag")
            : memberList[i].activeFlag == 1
            ? tcg.lib.prop("ac_link_manager_status_option_2")
            : memberList[i].activeFlag == 2
            ? tcg.lib.prop("ac_active_flag")
            : memberList[i].activeFlag == 5
            ? tcg.lib.prop("ac_active_flag")
            : memberList[i].activeFlag == 6
            ? tcg.lib.prop("ac_active_flag")
            : memberList[i].activeFlag == 7
            ? tcg.lib.prop("ac_active_flag_7")
            : "N/A";
        var rebates = {};
        for (var j = 0; j < memberList[i].rebates.length; j++) {
          rebates[memberList[i].rebates[j].rebateType] = memberList[i].rebates[j].rebateValue;
        }
        var accountBal = tcg.ac.customCurrencyFormat(memberList[i].availBalance, decimal);
        if (memberList[i].hasDirect) {
          downlineClass = "showdetail";
        }
        if (i % 2 == 0) {
          list += '<div class="divTableRow border-bot clearfix">';
        } else {
          list += '<div class="divTableRow border-bot clearfix divTableRowothr">';
        }

        var _status;
        if (memberList[i].customerName == sessionStorage.username) {
          _status = "active";
        } else {
          _status = memberList[i].onlineStatus ? "active" : "inactive";
        }
        list += '<div class="divTableCell"> <span class="_dot  ' + _status + '"></span></div>';
        list +=
          '<div class="divTableCell div-x tbl-link ' +
          downlineClass +
          '" data-downline=' +
          memberList[i].customerId +
          " data-downlinename=" +
          memberList[i].customerName +
          ">" +
          memberList[i].customerName +
          "</div>";
        downlineClass = "";
        list +=
          '<div class="divTableCell ac_memberType">' +
          (memberList[i].isAgent ? tcg.lib.prop("label_agent") : tcg.lib.prop("label_member")) +
          "</div>";
        list += '<div class="divTableCell ac_teamSize">' + memberList[i].teamCount + "</div>";

        var click_reb_text = rebates.SSC_1 ? rebates.SSC_1 : tcg.lib.prop("ac_clickable_rebate");

        list +=
          '<div class="divTableCell rebateSlide _ac_status_rebates" data-recomender="' +
          memberList[i].recommenderId +
          '" data-rebates=' +
          JSON.stringify(rebates) +
          " data-downline=" +
          memberList[i].customerId +
          " data-downlinename=" +
          memberList[i].customerName +
          ">" +
          click_reb_text +
          "</div>";
        list += '<div class="divTableCell regDate div-z ac_registerDate">' + registerDate + "</div>";
        list += '<div class="divTableCell regDate div-z">' + lastLoginTime + "</div>";
        list +=
          '<div class="divTableCell" data-switchDecimal="' +
          decimal +
          '"  data-value="' +
          accountBal.replace(/[^0-9.]/g, "") +
          '">' +
          accountBal +
          "</div>";

        var hide_gear = `${memberList[i].customerId}` === `${tcg.ac.customerId}` ? "hide" : "";
        if (sessionStorage.merchantCode == "hele8th") {
          hide_gear = "hide";
        }
        list +=
          '<div class="divTableCell div-y ' +
          hide_gear +
          '"><div class="tbl-gear game-icons">' +
          '<ul class="dropdown-opts hide"><span class="arrow-up"></span>';
        if (+memberList[i].recommenderId === tcg.ac.customerId) {
          list +=
            '<li class="setRebateDropdown" data-customer_id="' +
            memberList[i].customerId +
            '" data-customer_name="' +
            memberList[i].customerName +
            '">' +
            tcg.lib.prop("ac_rebate_set") +
            "</li>";
        }
        var _qt = JSON.parse(sessionStorage.qt);

        if (
          +memberList[i].recommenderId === tcg.ac.customerId &&
          memberList[i].isAgent &&
          !$.isEmptyObject(_qt.value)
        ) {
          list += '<li class="TransferQuota">' + tcg.lib.prop("ac_transfer_quota") + "</li>";
        }
        list +=
          '<li class="bettingHistoryLink" data-customer_name="' +
          memberList[i].customerName +
          '">' +
          tcg.lib.prop("ac_game_record") +
          "</li>";
        // if (isTransferEnabled == 1 && $("#breadcrumbs div").length == 1) {
        //     list += '<li class="transferToDown" data-downline="' + memberList[i].customerName + '">' +tcg.lib.prop("ac_transfer_to_downline")+ '</li>';
        // }
        if (footer.toDownline == "1" && $("#breadcrumbs div").length >= 1) {
          if (footer.downlineReceiver == "DIRECT" && memberList[i].recommenderId == globalVar.cid) {
            list +=
              '<li class="transferToDown" data-downline="' +
              memberList[i].customerName +
              '">' +
              tcg.lib.prop("ac_transfer_to_downline") +
              "</li>";
          }
          if (footer.downlineReceiver == "TREE") {
            list +=
              '<li class="transferToDown" data-downline="' +
              memberList[i].customerName +
              '">' +
              tcg.lib.prop("ac_transfer_to_downline") +
              "</li>";
          }
        }
        if (!memberList[i].isAgent && +memberList[i].recommenderId === tcg.ac.customerId) {
          list += '<li class="setAgent">' + tcg.lib.prop("ac_set_agent") + "</li>";
        }
        if (+memberList[i].recommenderId === tcg.ac.customerId) {
          list +=
            '<li class="sendMessage" data-customerName="' +
            memberList[i].customerName +
            '" data-customerId="' +
            memberList[i].customer_id +
            '" >' +
            tcg.lib.prop("ac_sent_a_message") +
            "</li>";
        }
        list += "</ul></div></div>";
        list += '<div class="entry" style="display:none;">' + JSON.stringify(memberList[i]) + "</div>";
        list += "</div>";
        // });
        teamBalance += memberList[i].availBalance;
        teamSize += memberList[i].teamCount;
      }
    }
    // list = '';
    if (footer.subTeamList && footer.subTeamList.length) {
      tcg.ac.ui.appendBreadCrumbs(footer.subTeamList);
    }

    $("#memberManagementList").html(list);

    if ($.isEmptyObject(globalVar.globeRebate)) {
      $(".divTableCell.rebateSlide._ac_status_rebates").hide();
      $("#memberManagement .ac_rebate_head").hide();
    }

    $("#memberManagementList")
      .off("click", ".rebateSlide")
      .on("click", ".rebateSlide", function () {
        $("#registerSubordinateFloater ._head span").html($(this).attr("data-downlinename"));
        var reb_lott = "";
        var reb_oothers = "";
        var rebates = JSON.parse($(this).attr("data-rebates"));
        var lott_html = "";
        var others_html = "";

        for (var gr in globalVar.globeRebate) {
          for (var i = globalVar.globeRebate[gr].length - 1; i >= 0; i--) {
            for (var r in rebates) {
              if (r == globalVar.globeRebate[gr][i].gameType) {
                if (gr == 4) {
                  lott_html +=
                    "<div class='rebs'>" + tcg.lib.prop("series_label_" + r) + " : " + rebates[r] + " </div>";
                } else {
                  others_html +=
                    "<div class='rebs'>" + tcg.lib.prop("series_label_" + r) + " : " + rebates[r] + " </div>";
                }
              }
            }
          }
        }

        $("#registerSubordinateFloater ._edit_btn.setRebate").attr({
          "data-customer_id": $(this).attr("data-downline"),
          "data-customer_name": $(this).attr("data-downlinename"),
        });

        $("#registerSubordinateFloater ._lott ._content").html("");
        if (lott_html) {
          $("#registerSubordinateFloater ._lott ._content").html(lott_html);
          $("#registerSubordinateFloater ._lott").show();
        }

        $("#registerSubordinateFloater ._others ._content").html("");
        if (others_html) {
          $("#registerSubordinateFloater ._others ._content").html(others_html);
          $("#registerSubordinateFloater ._others").show();
        }

        if ($(this).attr("data-recomender") !== `${tcg.ac.customerId}`) {
          $("#registerSubordinateFloater ._edit_btn.setRebate").hide();
        } else {
          $("#registerSubordinateFloater ._edit_btn.setRebate").show();
        }

        $("#registerSubordinateFloater").show("slide", { direction: "right" }, 300);
      });

    $("#registerSubordinateFloater .clos_btn")
      .off("click")
      .on("click", function () {
        $("#registerSubordinateFloater").toggle("slide", { direction: "right" }, 300);
      });

    // switch(tcg.ac.getMerchant()){
    //  case "500cai":
    //      // TCG-6214
    //      $(".mm-th7, .ac_super6Rebate").remove();
    //  break;
    //  default:
    // }

    $("#totalTeamSize").html(teamSize);
    $("#totalPlannedAmount").html(tcg.ac.customCurrencyFormat(teamBalance, decimal)).attr({
      "data-SwitchDecimal": decimal,
      "data-value": teamBalance,
    });
  };
  tcg.ac.ui.valueCheck = function (value, replacement) {
    if (value === undefined || value === null) {
      return replacement;
    } else {
      return value;
    }
  };
  tcg.ac.ui.loadPagination = function (type, currentPage, pageTotal, id) {
    var offSetButtons = 3;
    var currentPage = currentPage * 1;
    var totalButtons = offSetButtons * 2;
    var currentPageClass = null,
      _html = [];
    _html.push(
      "<div class='pag-arr-left game-icons pag-bnt'><a href='javascript:void(0)' data-pageNav='prev'>&nbsp;</a></div>"
    );
    var startPage = currentPage <= pageTotal - totalButtons ? currentPage : pageTotal - totalButtons;
    for (var i = startPage <= 0 ? 1 : startPage * 1; i <= pageTotal; i++) {
      if (i < offSetButtons + currentPage || i > pageTotal - offSetButtons) {
        currentPageClass = currentPage == i ? "active" : "";
        _html.push(
          "<div class='pag-num game-icons pag-bnt " +
            currentPageClass +
            "'><a href='javascript:void(0);' data-pageNo='" +
            i +
            "'>" +
            i +
            "</a></div>"
        );
      } else if (i == offSetButtons + currentPage) {
        _html.push("<div class='pag-bnt'>...</div>");
      }
    }
    _html.push(
      "<div class='pag-arr-right game-icons pag-bnt'><a href='javascript:void(0);' data-pageNav='next'>&nbsp;</a></div>"
    );

    _html.push("<div class='pag-search-con inline-block'>");
    _html.push("<input type='text' class='pag-search game-icons' name='inputPageNo'  />");
    _html.push(
      "<input class='game-icons switch-dete' type='button' name='goToPage' value='" +
        tcg.lib.prop("ac_datepicker_closeText") +
        "' />"
    );
    _html.push("</div>");
    var id = id || "#pagination";
    $(id).html(_html);
    tcg.ac.pageNav(type, currentPage, pageTotal, id);
    tcg.ac.clickPageNo(type, currentPage, pageTotal, id);
    tcg.ac.goToPageNo(type, currentPage, pageTotal, id);
  };
  tcg.ac.ui.loadPaginationNew = function (type, currentPage, pageTotal, id) {
    var offSetButtons = 3;
    var currentPage = currentPage * 1;
    var totalButtons = offSetButtons * 2;
    var currentPageClass = null,
      _html = [];
    _html.push('<a href="javascript:void(0)"  data-pageNav="prev" class="pageNum stepLeft"></a>');
    // _html.push("<div class='pag-arr-left game-icons pag-bnt'><a href='javascript:void(0)' data-pageNav='prev'>&nbsp;</a></div>");
    var startPage = currentPage <= pageTotal - totalButtons ? currentPage : pageTotal - totalButtons;
    for (var i = startPage <= 0 ? 1 : startPage * 1; i <= pageTotal; i++) {
      if (i < offSetButtons + currentPage || i > pageTotal - offSetButtons) {
        currentPageClass = currentPage == i ? "checkedPage" : "";
        // currentPageClass = currentPage == i ? "active" : "";
        _html.push(
          '<a href="javascript:void(0)" class="pageNum ' + currentPageClass + '" data-pageNo="' + i + '">' + i + "</a>"
        );

        // _html.push("<div class='pag-num game-icons pag-bnt " + currentPageClass + "'><a href='javascript:void(0);' data-pageNo='" + i + "'>" + i + "</a></div>");
      } else if (i == offSetButtons + currentPage) {
        _html.push('<a  href="javascript:void(0)"  class="">...</a>');
        // _html.push("<div class='pag-bnt'>...</div>");
      }
    }
    _html.push('<a href="javascript:void(0)"  data-pageNav="next" class="pageNum stepRight"></a>');
    // _html.push("<div class='pag-arr-right game-icons pag-bnt'><a href='javascript:void(0);' data-pageNav='next'>&nbsp;</a></div>");

    // _html.push("<div class='pag-search-con inline-block'>");
    // _html.push("<input type='text' class='pag-search game-icons' name='inputPageNo'  />");
    // _html.push("<input class='game-icons switch-dete' type='button' name='goToPage' value='" +tcg.lib.prop("ac_datepicker_closeText")+ "' />");
    // _html.push("</div>");
    var id = id || "#pagination";
    $(id).html(_html);
    tcg.ac.pageNav(type, currentPage, pageTotal, id);
    tcg.ac.clickPageNo(type, currentPage, pageTotal, id);
    tcg.ac.goToPageNo(type, currentPage, pageTotal, id);
  };
  tcg.ac.ui.loadBettingRecord = function (result) {
    var list = "",
      _total = "",
      decimal = 2;
    var bettingList = result.list;
    if (bettingList != null) {
      if (bettingList.length > 0) {
        //bettingList.forEach(function(entry) {
        $.each(bettingList, function (index, entry) {
          //var createTime = entry.create_time.substring(0,entry.create_time.indexOf("."));
          var createTime = entry.create_time.split(" ")[0];
          var status = "";
          switch (entry.status) {
            case "2":
              status = tcg.lib.prop("ac_order_status_2");
              break;
            case "4":
              status = tcg.lib.prop("ac_order_status_4");
              break;
            case "5":
              status = tcg.lib.prop("ac_order_status_5");
              break;
            case "6":
              status = tcg.lib.prop("ac_order_status_6");
              break;
            case "7":
              status = tcg.lib.prop("ac_order_status_7");
              break;
            case "8":
              status = tcg.lib.prop("ac_order_status_8");
              break;
            case "12":
              status = tcg.lib.prop("ac_order_status_12");
              break;
          }
          list += '<div class="divTableRow border-bot clearfix">';
          list +=
            '<div class="divTableCell onel-th-x openItem" data-orderid="' +
            entry.order_detail_id +
            '" data-chasing="' +
            entry.chase +
            '" data-ordermasterid="' +
            entry.order_master_id +
            '">' +
            tcg.ac.shortenText(entry.order_num) +
            "</div>";
          list += '<div class="divTableCell onel-th-x">' + entry.game_name.replace(/[A-Za-z]/g, "") + "</div>";
          list += '<div class="divTableCell onel-th-x">' + tcg.ac.formatDateFull(createTime, "yyyy-MM-dd") + "</div>";
          list +=
            '<div class="divTableCell onel-th-x rs-td-amt" data-switchDecimal="' +
            decimal +
            '" data-value="' +
            entry.actual_bet_amount +
            '" >' +
            tcg.ac.customCurrencyFormat(entry.actual_bet_amount, decimal) +
            "</span></div>";
          list +=
            '<div class="divTableCell onel-th-x rs-td-amt tbl-red" data-switchDecimal="' +
            decimal +
            '" data-value="' +
            entry.win_amount +
            '">' +
            tcg.ac.customCurrencyFormat(entry.win_amount, decimal) +
            "</span></div>";
          list += '<div class="divTableCell onel-th-x tbl-red">' + status + "</div>";
          list += "</div>";
        });
      } else {
        list += "<div class='tableContent-wrp'>";
        list += "<div class='noResult-data'>" + tcg.lib.prop("no_result") + "</div>";
        list += "</div>";
      }
      var total = result.footer,
        totalBetAmount = bettingList.length == 0 ? 0 : total.total_actual_bet_amount,
        totalWinAmount = bettingList.length == 0 ? 0 : total.total_win_amount;
      $("#gameHistoryList").html(list);

      _total += "<div class='tbl-total-con inline-block'>" + tcg.lib.prop("ac_bet_amount_total") + ":";
      _total +=
        "<span class='tbl-total-amt inline-block' data-SwitchDecimal='" +
        decimal +
        "' data-value='" +
        totalBetAmount +
        "'>" +
        tcg.ac.customCurrencyFormat(totalBetAmount, decimal) +
        "</span>";
      _total += "</div>";
      _total += "<div class='tbl-total-con inline-block'>" + tcg.lib.prop("ac_total_amount_prize") + ":";
      _total +=
        "<span class='tbl-total-amt inline-block' data-SwitchDecimal='" +
        decimal +
        "' data-value='" +
        totalWinAmount +
        "'>" +
        tcg.ac.customCurrencyFormat(totalWinAmount, decimal) +
        "</span>";
      _total += "</div>";

      $("#agentGameHistoryTotal").html(_total);

      $("#agentGameHistoryForm input[name='orderNum']").on("change", function () {
        $(this).val($(this).val().toUpperCase());
      });
    }
  };
  tcg.ac.ui.loadQueryConditionList = function (headers, callback) {
    tcg.ac.api.queryConditions(function (result) {
      var obj = {};
      var gamesArr = [];
      var orderStatus = "";
      var games = "<option value=''>" + tcg.lib.prop("ac_all") + "</option>";
      for (var x in result.games) {
        games += "<option value='" + x + "'>" + tcg.lib.prop("gameName_" + result.games[x]) + "</option>";
        gamesArr.push({ id: x, gameName: tcg.lib.prop("gameName_" + result.games[x]) });
      }
      obj.games = games;
      obj.gamesArr = gamesArr;
      for (var i in result.orderStatus) {
        orderStatus +=
          "<option value='" + i + "'>" + tcg.lib.prop("orderStatus_" + result.orderStatus[i]) + "</option>";
      }
      obj.orderStatus = orderStatus;
      callback(obj);
    });
  };
  tcg.ac.ui.searchAgentPvpGameHistory = function (downlineId) {
    $(document)
      .off("click", "#agentPvpGameHistoryForm .form-submit")
      .on("click", "#agentPvpGameHistoryForm .form-submit", function () {
        tcg.ac.getAgentPvpGameHistory(downlineId);
      });
  };
  tcg.ac.ui.loadAgentPvpGameHistory = function (result, gameType) {
    var _html = "",
      _total = "",
      decimal = 2,
      orders = result.list; //, total = result.footer;
    if (orders.length > 0) {
      for (var i = 0; i < orders.length; i++) {
        _html += "<div class='divTableRow border-bot clearfix'>";
        _html += "<div class='divTableCell onel-th-x'>" + orders[i].customer_name + "</div>";
        _html += "<div class='divTableCell onel-th-x'>" + orders[i].game_name + "</div>";
        _html +=
          "<div class='divTableCell onel-th-x rs-td-amt' data-switchDecimal='" +
          decimal +
          "' data-value='" +
          orders[i].game_loses +
          "'>" +
          tcg.ac.customCurrencyFormat(orders[i].game_loses, decimal) +
          "</span></div>";
        _html +=
          "<div class='divTableCell onel-th-x rs-td-amt' data-switchDecimal='" +
          decimal +
          "' data-value='" +
          orders[i].game_winnings +
          "'>" +
          tcg.ac.customCurrencyFormat(orders[i].game_winnings, decimal) +
          "</span></div>";
        _html +=
          "<div class='divTableCell onel-th-x' data-switchDecimal='" +
          decimal +
          "' data-value='" +
          orders[i].net_profit +
          "'>" +
          tcg.ac.customCurrencyFormat(orders[i].net_profit, decimal) +
          "</div>";
        _html += "</div>";
      }
    } else {
      _html += "<div class='tableContent-wrp'>";
      _html += "<div class='noResult-data'>" + tcg.lib.prop("no_result") + "</div>";
      _html += "</div>";
    }

    $("#agentPvpGameHistoryTable .betLoss").text(
      gameType == "PVP" ? tcg.lib.prop("ac_net_money") : tcg.lib.prop("ac_bet_amount")
    );
    $("#agentPvpGameHistoryTable .prizeWon").text(
      gameType == "PVP" ? tcg.lib.prop("ac_net_amount_of_money") : tcg.lib.prop("ac_winning_amount")
    );
    $("#agentPvpGameHistoryList").html(_html);
  };
  tcg.ac.ui.loadGameRooms = function (targetId, gameType, type) {
    var rng = "",
      pvp = "";

    rng += "<option value='game_ag_6'>" + tcg.lib.prop("ac_game_fish") + "</option>";
    pvp += "<option value=''>" + tcg.lib.prop("ac_all") + "</option>";
    pvp += "<option value='bjl_room'>" + tcg.lib.prop("ac_game_baccarat") + "</option>";
    pvp += "<option value='gswz_room'>" + tcg.lib.prop("ac_game_5_in_hk") + "</option>";
    pvp += "<option value='thirteen_room'>" + tcg.lib.prop("ac_game_13_water") + "</option>";
    pvp += "<option value='tbnn_room'>" + tcg.lib.prop("ac_game_tiberius") + "</option>";
    pvp += "<option value='nn_room'>" + tcg.lib.prop("ac_game_taurus") + "</option>";
    pvp += "<option value='zjh_room'>" + tcg.lib.prop("ac_game_zhunjinhua") + "</option>";
    $(targetId).html(gameType == 0 || gameType == "PVP" ? pvp : rng);

    var type = type || "set";
    if (type == "set") {
      tcg.ac.ui.customSelect(targetId);
    } else {
      $(targetId).trigger("chosen:updated");
    }
  };
  tcg.ac.ui.selectGameType = function (selector, targetId) {
    $(document)
      .off("change", selector)
      .on("change", selector, function () {
        var gameType = $(this).val();
        tcg.ac.ui.loadGameRooms(targetId, gameType, "update");
      });
  };
  tcg.ac.ui.loadTransferSumbit = function (downlineName) {
    $(document)
      .off("click", "#transferToDownlineForm .form-submit")
      .on("click", "#transferToDownlineForm .form-submit", function () {
        tcg.ac.submitTransferToDown(downlineName);
      });
    $(document)
      .off("keyup", "#transferToDownline [name='transferAmt']")
      .on("keyup", "#transferToDownline [name='transferAmt']", function () {
        $(".bl-data-hover").html($("[name='transferAmt']").val());
      });
  };
  tcg.ac.ui.validateTransferToDownlineInput = function () {
    $(document)
      .off("keyup", "#transferToDownlineForm .form-control")
      .on("keyup", "#transferToDownlineForm .form-control", function () {
        tcg.ac.ui.validateTransferToDownlineForm();
      });
  };
  tcg.ac.ui.validateTransferToDownlineForm = function () {
    $("#transferToDownlineForm .member_ContentCh .ch_mManagement .on-Mod").removeAttr("name");
    // $('#transferToDownlineForm')[0].childNodes[3].childNodes[5].childNodes[3].removeAttribute("name");
    var form = $("#transferToDownlineForm"),
      amount = form.find("[name='transferAmt']"),
      password = form.find("[name='paymentpwd']");
    if (password.val() == "" || amount.val() == "") {
      form.removeClass("enable");
    } else {
      if (tcg.ac.regExPattern("amount", amount.val())) {
        var amountVal = amount.val().split(".");
        if (amountVal[1] == undefined || amountVal[1].length <= 2) {
          form.addClass("enable");
        } else {
          form.removeClass("enable");
        }
      } else {
        form.removeClass("enable");
      }
    }
  };
  tcg.ac.ui.addNewContractRow = function (data) {
    var curr_inc = $("#add_new_contract_form .rec_increment").html() * 1;
    curr_inc++;
    var _html = "";
    _html += '<div  class="divident-content-wrp row_has_val " id="con_row_index_' + (curr_inc - 1) + '">';
    _html += '<div class="d-rnumber">' + (curr_inc - 1) + "</div>";
    _html += '<div class="betVolume">' + data.betVolume + "</div>";
    _html += '<div class="activeMember">' + data.activeMember + "</div>";
    _html += '<div class="requestedRate">' + data.requestedRate + "</div>";
    $(".delete_con_row").html("-").removeClass("action-btn");
    _html += '<div class="  delete_con_row"><span class="action-btn" id="delete_con_row">删除</span></div>';
    _html += "</div>";
    $("#add_new_contract_form .rec_increment").html(curr_inc);
    $("#add_new_contract_form").before(_html);
  };
  tcg.ac.ui.addNewContractRowCopy = function (data) {
    var curr_inc = $("#add_new_contract_form .rec_increment").html() * 1;
    curr_inc++;
    var _html = "";
    _html += '<div  class="divident-content-wrp row_has_val " id="con_row_index_' + (curr_inc - 1) + '">';
    _html += '<div class="d-rnumber">' + (curr_inc - 1) + "</div>";
    _html += '<div class="totalBet">' + data.totalBet + "</div>";
    _html += '<div class="betVolume">' + data.betVolume + "</div>";
    _html += '<div class="activeMember">' + data.activeMember + "</div>";
    _html += '<div class="requestedRate">' + data.requestedRate + "</div>";
    $(".delete_con_row").html("-").removeClass("action-btn");
    _html += '<div class="delete_con_row"><span class="action-btn" id="delete_con_row">删除</span></div>';
    _html += "</div>";
    $("#add_new_contract_form .rec_increment").html(curr_inc);
    $("#add_new_contract_form").before(_html);
  };
  tcg.ac.ui.editAddNewContractRow = function (data, isAutomated) {
    var curr_inc = $("#edit_contract_form .rec_increment").html() * 1;
    curr_inc++;
    var _html = "";
    var requestid_senderid = data.requestId ? data.requestId : "";
    _html +=
      '<div  class="divident-content-wrp row_has_no_val " id="con_row_index_' +
      (curr_inc - 1) +
      '" data-senderid = ' +
      requestid_senderid +
      ">";
    _html += '<div class="d-rnumber">' + (curr_inc - 1) + "</div>";
    _html += '<div class="betVolume">' + data.betVolume + "</div>";
    _html += '<div class="activeMember">' + data.activeMember + "</div>";
    _html += '<div class="requestedRate">' + parseInt(data.requestedRate) + "</div>";
    $(".delete_con_row").html("-").removeClass("action-btn");
    _html +=
      '<div class=" delete_con_row"><span class="action-btn" id=delete_con_row>' +
      tcg.lib.prop("ac_delete_btn") +
      "</span></div>";
    _html += "</div>";
    $("#edit_contract_form .rec_increment").html(curr_inc);
    $("#edit_contract_form").before(_html);
  };
  tcg.ac.ui.editAddNewContractRowCopy = function (data, isAutomated) {
    var curr_inc = $("#edit_contract_form .rec_increment").html() * 1;
    curr_inc++;
    var _html = "";
    var requestid_senderid = data.requestId ? data.requestId : "";
    _html +=
      '<div  class="divident-content-wrp row_has_no_val " id="con_row_index_' +
      (curr_inc - 1) +
      '" data-senderid = ' +
      requestid_senderid +
      ">";
    _html += '<div class="d-rnumber">' + (curr_inc - 1) + "</div>";
    _html += '<div class="totalBet">' + data.totalBet + "</div>";
    _html += '<div class="betVolume">' + data.betVolume + "</div>";
    _html += '<div class="activeMember">' + data.activeMember + "</div>";
    _html += '<div class="requestedRate">' + parseInt(data.requestedRate) + "</div>";
    $(".delete_con_row").html("-").removeClass("action-btn");
    _html +=
      '<div class=" delete_con_row"><span class="action-btn" id=delete_con_row>' +
      tcg.lib.prop("ac_delete_btn") +
      "</span></div>";
    _html += "</div>";
    $("#edit_contract_form .rec_increment").html(curr_inc);
    $("#edit_contract_form").before(_html);
  };
  tcg.ac.ui.loadBonusDownlines = function (result, downlineContracts) {
    //  Declare
    var _html = "",
      registerAmount = 0;
    var status = {
      P: { status: tcg.lib.prop("ac_status_P") },
      A: { status: tcg.lib.prop("ac_status_A") },
      E: { status: tcg.lib.prop("ac_status_E") },
      C: { status: tcg.lib.prop("ac_status_C") },
      V: { status: tcg.lib.prop("ac_status_V") },
      R: { status: tcg.lib.prop("ac_status_R") },
      DEF: { status: tcg.lib.prop("ac_status_DEF") },
    };

    //  Show result
    var downline_contracts = {};
    if (downlineContracts.value.downline) {
      var downlineContracts = downlineContracts.value.downline;
      $(downlineContracts).map(function () {
        downline_contracts[this.receiverId] = this;
      });
    }
    try {
      if (result.length) {
        var status_mem = "DEF";
        for (var i = 0; i < result.length; i++) {
          var entry = result[i];
          var registerDate = entry.reg_date == null ? new Date() : entry.reg_date.time;
          var processed_date = "-",
            createdDate_date = "-";
          var log_action = "<div class='d-histLog'>-</div>";
          var add_contract_btn = "";
          add_contract_btn += '<div class="add_contract action-btn" data-targetCustomer="' + entry.customer_name + '">';
          add_contract_btn += tcg.lib.prop("ac_initiate_contract");
          add_contract_btn += "</div>";
          var dividend_info = '<div class="d-contact">-</div>';
          if (downline_contracts[entry.customer_id]) {
            status_mem = downline_contracts[entry.customer_id].status;
            if (downline_contracts[entry.customer_id].processedDate) {
              processed_date = tcg.ac.formatDateFull(
                downline_contracts[entry.customer_id].processedDate * 1,
                "yyyy-MM-dd hh:mm"
              );
              createdDate_date = tcg.ac.formatDateFull(
                downline_contracts[entry.customer_id].createdDate * 1,
                "yyyy-MM-dd hh:mm"
              );
            }
            log_action =
              "<div class='dividend_log d-histLog action-btn' data-customer_id='" +
              entry.customer_id +
              "'>" +
              tcg.lib.prop("ac_signing_history") +
              "</div>";
            dividend_info =
              '<div class="d-contact action-btn viewContractDetails" data-targetCustomer="' +
              entry.customer_name +
              '"data-status="' +
              downline_contracts[entry.customer_id].status +
              '" data-requestedRate="' +
              downline_contracts[entry.customer_id].contractRate +
              '" >' +
              tcg.lib.prop("label_details") +
              "</div>";
            if (downline_contracts[entry.customer_id].status == "A") {
              add_contract_btn = "";
              add_contract_btn += '<div class="action-btn">';
              add_contract_btn +=
                '<span class="pointer void_contract" data-targetCustomer="' +
                entry.customer_name +
                '" >' +
                tcg.lib.prop("ac_termination") +
                "</span> | ";
              add_contract_btn +=
                '<span class="pointer edit_contract" data-targetCustomer="' +
                entry.customer_name +
                '" data-status="' +
                downline_contracts[entry.customer_id].status +
                '" >' +
                tcg.lib.prop("ac_modify") +
                "</span>";
              add_contract_btn += "</div>";
            } else if (
              downline_contracts[entry.customer_id].status == "P" ||
              downline_contracts[entry.customer_id].status == "V"
            ) {
              add_contract_btn = "";
              add_contract_btn += '<div class="">';
              add_contract_btn += tcg.lib.prop("ac_waiting_to_determined");
              add_contract_btn += "</div>";
            } else if (downline_contracts[entry.customer_id].status == "E") {
              add_contract_btn = "";
              add_contract_btn += '<div class="">';
              add_contract_btn += tcg.lib.prop("ac_waiting_to_determined");
              add_contract_btn += "</div>";
            }
          }
          _html += '<div class="d-cont-wrp">';
          _html += '<div class="d-name">' + entry.customer_name + "</div>";
          _html += dividend_info;
          _html += '<div class="d-time">' + createdDate_date + "</div>";
          _html += '<div class="d-apTime">' + processed_date + "</div>";
          if (downline_contracts[entry.customer_id]) {
            _html += '<div class="d-status ">' + status[status_mem].status + "</div>";
          } else {
            _html += '<div class="d-status">' + tcg.lib.prop("ac_status_DEF") + "</div>";
          }
          _html += log_action;
          _html += add_contract_btn;
          _html += "</div>";
        }
      } else {
        _html += "<div class='tableContent-wrp'><div class='noResult-data'>" + tcg.lib.prop("no_result") + "</div>";
      }
    } catch (e) {
      _html += "<div class='tableContent-wrp'><div class='noResult-data'>" + tcg.lib.prop("no_result") + "</div>";
    }

    $("#downlineList").html(_html);
    $("#totalEffectiveAmount").text(registerAmount);
  };
  tcg.ac.ui.loadBonusDownlinesCopy = function (result, downlineContracts) {
    //  Declare
    var _html = "",
      registerAmount = 0;
    var status = {
      P: { status: tcg.lib.prop("ac_status_P") },
      A: { status: tcg.lib.prop("ac_status_A") },
      E: { status: tcg.lib.prop("ac_status_E") },
      C: { status: tcg.lib.prop("ac_status_C") },
      V: { status: tcg.lib.prop("ac_status_V") },
      R: { status: tcg.lib.prop("ac_status_R") },
      DEF: { status: tcg.lib.prop("ac_status_DEF") },
    };

    //  Show result
    var downline_contracts = {};
    if (downlineContracts.value.downline) {
      var downlineContracts = downlineContracts.value.downline;
      $(downlineContracts).map(function () {
        downline_contracts[this.receiverId] = this;
      });
    }
    try {
      if (result.length) {
        var status_mem = "DEF";
        for (var i = 0; i < result.length; i++) {
          var entry = result[i];
          var registerDate = entry.reg_date == null ? new Date() : entry.reg_date.time;
          var processed_date = "-",
            createdDate_date = "-";
          var log_action = "<div class='d-histLog'>-</div>";
          var add_contract_btn = "";
          add_contract_btn += '<div class="add_contract action-btn" data-targetCustomer="' + entry.customer_name + '">';
          add_contract_btn += tcg.lib.prop("ac_initiate_contract");
          add_contract_btn += "</div>";
          var dividend_info = '<div class="d-contact">-</div>';
          if (downline_contracts[entry.customer_id]) {
            status_mem = downline_contracts[entry.customer_id].status;
            if (downline_contracts[entry.customer_id].processedDate) {
              processed_date = tcg.ac.formatDateFull(
                downline_contracts[entry.customer_id].processedDate * 1,
                "yyyy-MM-dd hh:mm"
              );
              createdDate_date = tcg.ac.formatDateFull(
                downline_contracts[entry.customer_id].createdDate * 1,
                "yyyy-MM-dd hh:mm"
              );
            }
            log_action =
              "<div class='dividend_log d-histLog action-btn' data-customer_id='" +
              entry.customer_id +
              "'>" +
              tcg.lib.prop("ac_signing_history") +
              "</div>";
            dividend_info =
              '<div class="d-contact action-btn viewContractDetails" data-targetCustomer="' +
              entry.customer_name +
              '"data-status="' +
              downline_contracts[entry.customer_id].status +
              '"data-requestedRate="' +
              downline_contracts[entry.customer_id].contractRate +
              '" >' +
              tcg.lib.prop("label_details") +
              "</div>";
            if (downline_contracts[entry.customer_id].status == "A") {
              add_contract_btn = "";
              add_contract_btn += '<div class="action-btn">';
              add_contract_btn +=
                '<span class="pointer void_contract" data-targetCustomer="' +
                entry.customer_name +
                '" >' +
                tcg.lib.prop("ac_termination") +
                "</span> | ";
              add_contract_btn +=
                '<span class="pointer edit_contract" data-targetCustomer="' +
                entry.customer_name +
                '" data-status="' +
                downline_contracts[entry.customer_id].status +
                '">' +
                tcg.lib.prop("ac_modify") +
                "</span>";
              add_contract_btn += "</div>";
            } else if (
              downline_contracts[entry.customer_id].status == "P" ||
              downline_contracts[entry.customer_id].status == "V"
            ) {
              add_contract_btn = "";
              add_contract_btn += '<div class="">';
              add_contract_btn += tcg.lib.prop("ac_waiting_to_determined");
              add_contract_btn += "</div>";
            } else if (downline_contracts[entry.customer_id].status == "E") {
              add_contract_btn = "";
              add_contract_btn += '<div class="">';
              add_contract_btn += tcg.lib.prop("ac_waiting_to_determined");
              add_contract_btn += "</div>";
            }
          }
          _html += '<div class="d-cont-wrp">';
          _html += '<div class="d-name">' + entry.customer_name + "</div>";
          _html += dividend_info;
          _html += '<div class="d-time">' + createdDate_date + "</div>";
          _html += '<div class="d-apTime">' + processed_date + "</div>";
          if (downline_contracts[entry.customer_id]) {
            _html += '<div class="d-status ">' + status[status_mem].status + "</div>";
          } else {
            _html += '<div class="d-status">' + tcg.lib.prop("ac_status_DEF") + "</div>";
          }
          _html += log_action;
          _html += add_contract_btn;
          _html += "</div>";
        }
      } else {
        _html += "<div class='tableContent-wrp'><div class='noResult-data'>" + tcg.lib.prop("no_result") + "</div>";
      }
    } catch (e) {
      _html += "<div class='tableContent-wrp'><div class='noResult-data'>" + tcg.lib.prop("no_result") + "</div>";
    }

    $("#downlineList").html(_html);
    $("#totalEffectiveAmount").text(registerAmount);
  };
  tcg.ac.ui.loadContractHistoryLog = function (result) {
    var _html = "";
    for (var i = 0; i < result.value.length; i++) {
      var elem = result.value[i];
      var placeholder = "";
      switch (elem.action) {
        case "N":
          placeholder = tcg.lib.prop("ac_initiate_contract");
          break;
        case "E":
          placeholder = tcg.lib.prop("ac_modify_contract");
          break;
        case "C":
          placeholder = tcg.lib.prop("ac_termination_success");
          break;
        case "A":
          placeholder = tcg.lib.prop("ac_signed_success");
          break;
        case "R":
          placeholder = tcg.lib.prop("ac_termination");
          break;
        case "V":
          placeholder = tcg.lib.prop("ac_initiate_termination");
          break;
      }
      _html += '<div  class="divident-content-wrp ">';
      _html += '<div class="">' + tcg.ac.timeToDateFormat(elem.activityDate, "dateTime") + "</div>";
      _html += '<div class="">' + placeholder + "</div>";
      _html += "</div>";
    }
    $("#dividendLogList").html(_html);
  };
  tcg.ac.ui.appendContractDivInfo = function (data) {
    $("#agentContract").append(
      '<div class="dvdentRport-content" data-minimumBet="' +
        data.netLoss +
        '" data-activeMember="' +
        data.activeMember +
        '" data-contractRate="' +
        Math.round(data.contractRate * 100) +
        '"><div class="po-middle team-betting">' +
        tcg.lib.prop("ac_half_month") +
        "≥" +
        data.netLoss +
        tcg.lib.prop("ac_yuan") +
        '</div> <div class="po-middle team-active">' +
        data.activeMember +
        tcg.lib.prop("label_unit_people") +
        '</div> <div class="po-middle dvdent-rate">' +
        Math.round(data.contractRate * 100) +
        "%</div></div>"
    );
  };
  tcg.ac.ui.appendContractDivInfoCopy = function (data) {
    $("#agentContract").append(
      '<div class="dvdentRport-content" data-totalBet="' +
        data.minimumBet +
        '" data-minimumBet="' +
        data.netLoss +
        '" data-activeMember="' +
        data.activeMember +
        '" data-contractRate="' +
        Math.round(data.contractRate * 100) +
        '"><div class="po-middle team-betting">' +
        data.minimumBet +
        tcg.lib.prop("ac_yuan") +
        '</div><div class="po-middle team-minimumBet">' +
        data.netLoss +
        tcg.lib.prop("ac_yuan") +
        '</div> <div class="po-middle team-active">' +
        data.activeMember +
        tcg.lib.prop("label_unit_people") +
        '</div> <div class="po-middle dvdent-rate">' +
        Math.round(data.contractRate * 100) +
        "%</div></div>"
    );
  };
  tcg.ac.ui.loadTeamOverview = function (result, agInfo) {
    var newGetList = result.value.list;
    var agInfo = agInfo.value;

    $("#hagDirectlyUnder > span").text(
      agInfo.currentDirectSubordinateCount > 0 ? agInfo.currentDirectSubordinateCount : " - "
    );

    $("#allTeamMember >span").text(agInfo.currentTeamMemberCount > 0 ? agInfo.currentTeamMemberCount : " - ");

    $("#findLottBlances > span").html(agInfo.currentBalance > 0 ? agInfo.currentBalance : " - ");
  };

  tcg.ac.ui.fillGraphTab = function () {
    var newGetList = JSON.parse(sessionStorage.chartData).value.list;
    var game = $(".overviewGames_input").attr("data-value");
    var deposit = 0,
      withdraw = 0,
      lott_game_betting = 0,
      lott_game_winning = 0,
      lott_agent_commission = 0;

    data = newGetList[0][game].footer;
    deposit = data.totalDeposit;
    withdraw = data.totalWithdrawal;
    lott_game_betting = data.totalBet;
    lott_game_winning = data.totalWin;
    lott_agent_commission = data.totalRebate;

    $("#hag_directly_ctdvalue").html(
      '<div class="hag_directly_ctdm">' +
        '<p><span class="hag_directly_ctdsbg1">' +
        tcg.lib.prop("overview_label22") +
        "</span></p>" +
        "<p>" +
        tcg.ac.customCurrencyFormat(deposit /* + agent_transfer_in*/, 2) +
        "</p>" +
        "</div>" +
        '<div class="hag_directly_ctdm">' +
        '<p><span class="hag_directly_ctdsbg2">' +
        tcg.lib.prop("overview_label23") +
        "</span></p>" +
        "<p>" +
        tcg.ac.customCurrencyFormat(withdraw /*+ agent_transfer_out*/, 2) +
        "</p>" +
        "</div>" +
        '<div class="hag_directly_ctdm">' +
        '<p><span class="hag_directly_ctdsbg3">' +
        tcg.lib.prop("overview_label24") +
        "</span></p>" +
        "<p>" +
        tcg.ac.customCurrencyFormat(lott_game_betting, 2) +
        "</p>" +
        "</div>" +
        '<div class="hag_directly_ctdm">' +
        '<p><span class="hag_directly_ctdsbg4">' +
        tcg.lib.prop("overview_label25") +
        "</span></p>" +
        "<p>" +
        tcg.ac.customCurrencyFormat(lott_game_winning, 2) +
        "</p>" +
        "</div>" +
        '<div class="hag_directly_ctdm">' +
        '<p><span class="hag_directly_ctdsbg5">' +
        tcg.lib.prop("overview_label26") +
        "</span></p>" +
        "<p>" +
        tcg.ac.customCurrencyFormat(lott_agent_commission, 2) +
        "</p>" +
        "</div>"
    );
  };

  tcg.ac.ui.loadContractDetails = function (result) {
    var _html = "",
      _addNum = 1,
      new_result = [];
    new_result = tcg.ac.dividendChangeListVlue(result.value.downline);
    if (new_result.length > 0) {
      for (var i = 0; i < new_result.length; i++) {
        var item = new_result[i];
        //if(item.status == "A"){
        _html += '<div  class="divident-content-wrp row_has_val ">';
        _html += '<div class="">' + _addNum + "</div>";
        _html += '<div class="">' + item.netLoss * 1 + "</div>";
        _html += '<div class="">' + item.activeMember * 1 + "</div>";
        _html += '<div class="">' + parseInt(item.contractRate * 100) + "%</div>";
        _html += '<div class="">-</div>';
        _html += "</div>";
        _addNum++;
        //}
      }
    }

    $("#downlineContractDetailsList").html(_html);
  };
  tcg.ac.ui.loadContractDetailsCopy = function (result) {
    var _html = "",
      _addNum = 1,
      new_result = [];
    new_result = tcg.ac.dividendChangeListVlue(result.value.downline);
    if (new_result.length > 0) {
      for (var i = 0; i < new_result.length; i++) {
        var item = new_result[i];
        //if(item.status == "A"){
        _html += '<div  class="divident-content-wrp row_has_val ">';
        _html += '<div class="">' + _addNum + "</div>";
        _html += '<div class="">' + item.minimumBet * 1 + "</div>";
        _html += '<div class="">' + item.netLoss * 1 + "</div>";
        _html += '<div class="">' + item.activeMember * 1 + "</div>";
        _html += '<div class="">' + parseInt(item.contractRate * 100) + "%</div>";
        _html += '<div class="">-</div>';
        _html += "</div>";
        _addNum++;
        //}
      }
    }

    $("#downlineContractDetailsList").html(_html);
  };
  tcg.ac.ui.loadCommissionReport = function (result) {
    var data = result.value.list[0];
    if (data) {
      $("#plan").attr("data-current", "").attr("data-prev", Math.round(data.dividend_income_plan, 2));
      $("#betting")
        .attr("data-current", Math.round(data.curr_game_betting, 2))
        .attr("data-prev", Math.round(data.team_betting, 2));
      $("#profit")
        .attr("data-current", Math.round(data.curr_net_profit, 2))
        .attr("data-prev", Math.round(data.prev_net_profit, 2));
      $("#active").attr("data-current", data.curr_active_member_count).attr("data-prev", data.active_member_count);
      $("#rate").attr("data-current", data.curr_dividend_rate).attr("data-prev", data.prev_dividend_rate);
      $("#income")
        .attr("data-current", Math.round(data.curr_dividend_income, 2))
        .attr("data-prev", Math.round(data.prev_dividend_income, 2));
    }
  };
  tcg.ac.ui.loadCommissionReportCopy = function (result, rs) {
    var current = result ? result.value : false;
    var prev = rs ? rs.value : false;
    if (current) {
      $("#cont1").attr("data-current", current.teamBetting);
      $("#cont2").attr("data-current", current.teamWinning);
      $("#cont3").attr("data-current", current.dailyIncome);
      $("#cont4").attr("data-current", current.teamAgentCommission);
      $("#cont5").attr("data-current", current.teamPromotion);
      $("#cont6").attr("data-current", current.teamRebate);
      $("#cont7").attr("data-current", current.teamProfitSharing);
      $("#cont8").attr("data-current", current.teamBankCharges);
      $("#cont9").attr("data-current", current.teamNetProfit);
      $("#cont10").attr("data-current", current.activeMember);
      $("#cont11").attr("data-current", current.rate);
      $("#cont12").attr("data-current", current.dividendAmount);
      $("#cont13").attr("data-current", current.teamProductCharge);
      $("#cont8").attr("data-currentStart", current.startDate).attr("data-currentEnd", current.endDate);
      $("#cont13").attr("data-currentStart", current.startDate).attr("data-currentEnd", current.endDate);
    }
    if (prev) {
      $("#cont1").attr("data-prev", prev.teamBetting);
      $("#cont2").attr("data-prev", prev.teamWinning);
      $("#cont3").attr("data-prev", prev.dailyIncome);
      $("#cont4").attr("data-prev", prev.teamAgentCommission);
      $("#cont5").attr("data-prev", prev.teamPromotion);
      $("#cont6").attr("data-prev", prev.teamRebate);
      $("#cont7").attr("data-prev", prev.teamProfitSharing);
      $("#cont8").attr("data-prev", prev.teamBankCharges);
      $("#cont9").attr("data-prev", prev.teamNetProfit);
      $("#cont10").attr("data-prev", prev.activeMember);
      $("#cont11").attr("data-prev", prev.rate);
      $("#cont12").attr("data-prev", prev.dividendAmount);
      $("#cont13").attr("data-prev", prev.teamProductCharge);
      $("#cont8").attr("data-prevStart", prev.startDate).attr("data-prevEnd", prev.endDate);
      $("#cont13").attr("data-prevStart", prev.startDate).attr("data-prevEnd", prev.endDate);
    }
  };
  tcg.ac.ui.loadBankCharges = function (result) {
    var items = [
      "pgBankCharges",
      "mtBankCharges",
      "unqrBankCharges",
      "unBankCharges",
      "alipayBankCharges",
      "maqrBankCharges",
      "atBankCharges",
      "aliwapBankCharges",
      "wechatBankCharges",
      "wtBankCharges",
      "wcwapBankCharges",
      "jdBankCharges",
      "qqBankCharges",
      "qqwapBankCharges",
      "wrsBankCharges",
      "jdwapBankCharges",
      "mwqrBankCharges",
      "muqrBankCharges",
      "eptBankCharges",
      "label_other",
    ];
    var data = result.footer;
    var _html = "";
    if (data) {
      for (var i = 0; i < items.length; i++) {
        _html += "<li><h4>" + tcg.lib.prop(items[i]) + "</h4>";
        _html += "<div><p>" + tcg.ac.currencyFormat(data[items[i]], 2) + "</p></div></li>";
      }
    }
    return _html;
  };
  tcg.ac.ui.showTooltip = function (elem, err_msg, customClass, callback) {
    var className = customClass || "form-tooltip";
    var _html = "<div class='err-msg tcg-tooltip " + className + "' style='display: block'>";
    _html += "<div class='err-cont' style='width:auto !important;'>";
    _html += "<span class='rs-tri'></span>";
    _html += "<span class='the-err-msg'>" + tcg.lib.prop(err_msg) + "</span>";
    _html += "</div></div>";
    elem.parents(".form-group").addClass("in-rel");
    elem.parents("form").find(".tcg-tooltip").remove();
    elem.after(_html);
    if (callback) {
      callback();
    } else {
      $(document)
        .off("click.tooltip", "html")
        .on("click.tooltip", "html", function (e) {
          if (!$(e.target).hasClass("form-submit")) {
            $("." + className).remove();
            $(document).off("click.tooltip");
          }
        });
    }
  };

  tcg.ac.ui.loadWalletList = function (id, walletList, callback) {
    var list = ""; // "<option value=''>请选择</option>";
    for (var i = 0; i < walletList.length; i++) {
      // if( tcg.lib.prop(walletList[i].accountName) != "[" +walletList[i].accountName+ "]" ){
      var walletName = tcg.lib.prop(walletList[i].accountName),
        selected = walletName == "主钱包" ? "selected=''" : "";
      list += "<option value='" + walletList[i].accountTypeId + "' " + selected + ">" + walletName + "</option>";

      // switch(tcg.ac.getMerchant()){
      // 	// TCG-6059
      // 	case "500cai":
      // 		if(walletList[i].accountTypeId == 1 || walletList[i].accountTypeId == 301){
      // 			walletList[i].accountTypeId = "DISABLED";
      // 		}
      // 	break;
      // 	default:
      // }

      // switch(walletList[i].accountTypeId) {
      //     case 1:
      //         var wallet_1 = "<option value='" +walletList[i].accountTypeId+ "' " +selected+ ">" +walletName+ "</option>";
      //         break;
      //     case 2:
      //         var wallet_2 = "<option value='" +walletList[i].accountTypeId+ "' " +selected+ ">" +walletName+ "</option>";
      //         break;
      //     case 301:
      //         var wallet_3 = "<option value='" +walletList[i].accountTypeId+ "' " +selected+ ">" +walletName+ "</option>";
      //         break;
      //     case 4:
      //         var wallet_4 = "<option value='" +walletList[i].accountTypeId+ "' " +selected+ ">" +walletName+ "</option>";
      //         break;
      // }
      // }
    }

    // list += wallet_2 || "";
    // list += wallet_4 || "";
    // list += wallet_1 || "";
    // list += wallet_3 || "";

    $(id).html(list);
    tcg.ac.ui.customSelect(id);
    callback();
  };
  tcg.ac.ui.searchDownlineTransactionDetails = function () {
    $("#downlineTransactionDetailsForm .form-submit")
      .unbind("click")
      .bind("click", function () {
        tcg.ac.getDownlineTransactionDetails();
      });
  };
  tcg.ac.ui.loadDownlineTransactionDetails = function (result, accountType) {
    var decimal = 2,
      list = "";
    if (result.list.length > 0) {
      for (var i = 0; i < result.list.length; i++) {
        if ([8122, 8222].indexOf(result.list[i].tx_type_id) < 0) {
          var dateTime = result.list[i].tx_time,
            date = dateTime.slice(0, dateTime.indexOf(" ")),
            time = dateTime.substr(dateTime.indexOf(" ") + 1),
            remark = result.list[i].remark,
            currentAvailBalance = result.list[i].current_avail_balance;

          if (result.list[i].amount < 0) {
            var amountColor = "tbl-green";
            var balPayment = tcg.lib.prop("ac_expenditure");
            var amount = result.list[i].amount;
          } else {
            // Filter Transaction Type '6201' and '2004'; To fix the issue of wrong response data of Transaction Type-'6201' and '2004' of MCS or ACS in Ticket, TCG-1323.
            var transType = result.list[i].tx_type_id;
            if (transType == "6201" || transType == "2004") {
              var amountColor = "tbl-green";
              var balPayment = tcg.lib.prop("ac_expenditure");
              var amount = "-" + result.list[i].amount;
            } else {
              var amountColor = "tbl-red";
              var balPayment = tcg.lib.prop("ac_income");
              var amount = result.list[i].amount;
            }
          }

          if (remark.indexOf("Lott_User_drawback") > 0) {
            remark = remark.substr(0, remark.indexOf("_Lott_User_drawback")) + ":Lott_User_drawback";
          }

          if (remark.indexOf(" LOTT") > 0) {
            remark = remark.split(" ");
            remark = remark[1];
          }

          if (remark.indexOf("game type : null") > 0) {
            remark = "gameName_NOGAME";
          }

          if (remark.indexOf(":") > 0) {
            var temp_remark = remark;
            temp_remark = temp_remark.split(":");
            if (temp_remark[1] == "Rebate") {
              remark = tcg.lib.prop("gameName_" + remark.split(":")[0]) + " : " + tcg.lib.prop("Lott_Rebate");
            } else if (temp_remark[1] == "Lott_User_drawback") {
              remark =
                tcg.lib.prop("gameName_" + remark.substr(0, remark.indexOf(":"))) +
                " : " +
                tcg.lib.prop("Lott_User_drawback");
            } else if (temp_remark[1] == "Lott_User_Cancel_Order") {
              remark =
                tcg.lib.prop("gameName_" + remark.substr(0, remark.indexOf(":"))) +
                " : " +
                tcg.lib.prop("Lott_User_Cancel_Order");
            } else {
              remark =
                tcg.lib.prop("gameName_" + remark.substr(0, remark.indexOf(":"))) +
                " : " +
                tcg.lib.prop(remark.substr(remark.indexOf(":") + 1));
            }
          }
          remark = tcg.lib.prop(remark);
          remark = remark.replace(/[\[\]&]+/g, "");
          var account = result.list[i].customer_name.split("@");
          if (
            $("[name='type'] :selected").val() == "4301" &&
            $("[name='type'] :selected").parent().attr("label") == tcg.lib.prop("ac_income")
          ) {
            if (amount < 0) {
              continue;
            }
          } else if (
            $("[name='type'] :selected").val() == "4301" &&
            $("[name='type'] :selected").parent().attr("label") == tcg.lib.prop("ac_expenditure")
          ) {
            if (amount > 0) {
              continue;
            }
          }
          list += "<div class='divTableRow border-bot clearfix'>";
          list += "<div class='divTableCell pp-pads dv-w-150'>" + account[1] + "</div>";
          list += "<div class='divTableCell pp-pads dv-w-100'>" + tcg.lib.prop(result.list[i].tx_type_id) + "</div>";
          list += "<div class='divTableCell pp-pads dv-w-50'>" + balPayment + "</div>";
          list +=
            "<div class='divTableCell pp-pads dv-w-150'>" + date + " <span class='tblDec'>" + time + "</span></div>";
          list +=
            "<div class='divTableCell pp-pads dv-w-140 " +
            amountColor +
            "' data-switchDecimal='" +
            decimal +
            "' data-value='" +
            amount +
            "'>" +
            tcg.ac.customCurrencyFormat(amount, decimal) +
            "</div>";
          list +=
            "<div class='divTableCell pp-pads dv-w-140' data-switchDecimal='" +
            decimal +
            "' data-value='" +
            currentAvailBalance +
            "'>" +
            tcg.ac.customCurrencyFormat(currentAvailBalance, decimal) +
            "</div>";
          list += "<div class='divTableCell pp-pads dv-w-210'>" + remark + "</div>";
          list += "</div>";
        }
      }
    } else {
      list += "<div class='tableContent-wrp'>";
      list += "<div class='noResult-data'>" + tcg.lib.prop("no_result") + "</div>";
      list += "</div>";
    }
    $("#downlineTransactionDetailsList").html(list);
  };
  tcg.ac.ui.loadDownlineTransactionDetailsCopy = function (result, accountType) {
    var list = "";
    if (result.list.length > 0) {
      for (var i = 0; i < result.list.length; i++) {
        list += "<div class='divTableRow border-bot clearfix'>";
        list += "<div class='divTableCell pp-pads dv-w-100'>" + result.list[i].customerName + "</div>";
        list += "<div class='divTableCell pp-pads dv-w-100'>" + result.list[i].nameCn + "</div>";
        list += "<div class='divTableCell pp-pads dv-w-150'>" + result.list[i].txTime + "</div>";
        list += "<div class='divTableCell pp-pads dv-w-140'>" + result.list[i].amount + "</div>";
        list += "<div class='divTableCell pp-pads dv-w-140'>" + result.list[i].currentAvailBalance + "</div>";
        var orderNo = result.list[i].orderNo;
        if (orderNo == null || orderNo == undefined) {
          orderNo = "-";
        }
        list += "<div class='divTableCell pp-pads dv-w-100'>" + orderNo + "</div>";
        list += "</div>";
      }
    } else {
      list += "<div class='tableContent-wrp'>";
      list += "<div class='noResult-data'>" + tcg.lib.prop("no_result") + "</div>";
      list += "</div>";
    }
    $("#totalAmount").html(tcg.ac.customCurrencyFormat(result.footer.amount, 2));
    $("#downlineTransactionDetailsList").html(list);
  };
  tcg.ac.ui.onChangeSelect = function (formId) {
    $(document)
      .off("change", formId + " [name='account']")
      .on("change", formId + " [name='account']", function () {
        tcg.ac.ui.changeOptions(formId + " [name='account']", formId + " [name='type']");
      });
  };
  tcg.ac.ui.changeOptions = function (account, type) {
    var select = $(account),
      type = $(type),
      selected = select.find("option:selected").val();

    type.remove("options");

    var prop = {
      width: "100%",
      disable_search: true,
    };
    $(account, type)
      .chosen(prop)
      .on("chosen:showing_dropdown", function () {
        $(account, type).parents("div").addClass("red-up");
      })
      .on("chosen:hiding_dropdown", function () {
        $(account, type).parents("div").removeClass("red-up");
      });

    switch (selected) {
      // PVP
      case "1":
        var options = "";
        options += '<option value="" class="entire" selected>' + tcg.lib.prop("ac_all") + "</option>";
        options += '<optgroup label="' + tcg.lib.prop("ac_income") + '">';
        options += '<option value="5100">' + tcg.lib.prop("ac_income_option_1") + "</option>";
        //	options += '<option value="5112">活动</option>';
        options += '<option value="8102">' + tcg.lib.prop("ac_income_option_2") + "</option>";
        options += "</optgroup>";

        options += '<optgroup label="' + tcg.lib.prop("ac_expenditure") + '">';
        options += '<option value="5200">' + tcg.lib.prop("ac_income_option_1") + "</option>";
        options += '<option value="8202">' + tcg.lib.prop("ac_income_option_2") + "</option>";
        options += "</optgroup>";

        $(type).html(options);
        break;

      // Main
      case "2":
        var options = "";
        options += '<option value="" class="entire" selected>' + tcg.lib.prop("ac_all") + "</option>";
        options += '<optgroup label="' + tcg.lib.prop("ac_income") + '">';
        options += '<option value="6101">' + tcg.lib.prop("ac_income_option_3") + "</option>";
        if (globalVar.enableDividentTransacationRecord) {
          options += '<option value="6116,6112">' + tcg.lib.prop("ac_income_option_4") + "</option>";
        }
        options += '<option value="6131,5112,3102,4104">' + tcg.lib.prop("ac_income_option_5") + "</option>";
        options += '<option value="4401">' + tcg.lib.prop("ac_revenue_report_column_4") + "</option>";
        if (globalVar.enableSalaryTransactionRecord) {
          options += '<option value="4301">' + tcg.lib.prop("ac_revenue_report_column_3") + "</option>";
        }
        options += '<option value="8102">' + tcg.lib.prop("ac_income_option_2") + "</option>";
        options += '<option value="6110">' + tcg.lib.prop("ac_income_option_6") + "</option>";
        options += '<option value="6104">' + tcg.lib.prop("ac_income_option_7") + "</option>";
        options += '<option value="6111,6109,6113">' + tcg.lib.prop("ac_income_option_8") + "</option>";
        options += "</optgroup>";

        options += '<optgroup label="' + tcg.lib.prop("ac_expenditure") + '">';
        options += '<option value="6201">' + tcg.lib.prop("ac_income_option_9") + "</option>";
        options += '<option value="8202">' + tcg.lib.prop("ac_income_option_2") + "</option>";
        options += '<option value="6208">' + tcg.lib.prop("ac_income_option_10") + "</option>";
        options += '<option value="6207">' + tcg.lib.prop("ac_income_option_7") + "</option>";
        if (globalVar.enableSalaryTransactionRecord) {
          options += '<option value="4301">' + tcg.lib.prop("ac_revenue_report_column_3") + "</option>";
        }
        options += "</optgroup>";

        $(type).html(options);
        break;

      // AG
      case "301":
        var options = "";
        options += '<option value="" class="entire" selected>' + tcg.lib.prop("ac_all") + "</option>";
        options += '<optgroup label="' + tcg.lib.prop("ac_income") + '">';
        options += '<option value="3116">' + tcg.lib.prop("ac_income_option_11") + "</option>";
        //options += '<option value="3102">活动</option>';
        options += '<option value="3101">' + tcg.lib.prop("ac_income_option_2") + "</option>";
        options += "</optgroup>";

        options += '<optgroup label="' + tcg.lib.prop("ac_expenditure") + '">';
        options +=
          '<option value="3216">' + tcg.lib.prop("ac_subordinate_management_pvp_history_column_3") + "</option>";
        options += '<option value="3201">' + tcg.lib.prop("ac_income_option_2") + "</option>";
        options += "</optgroup>";

        $(type).html(options);

        break;

      // Lotto
      case "4":
        var options = "";
        options += '<option value="" class="entire" selected>' + tcg.lib.prop("ac_all") + "</option>";
        options += '<optgroup label="' + tcg.lib.prop("ac_income") + '">';
        options += '<option value="4100">' + tcg.lib.prop("ac_income_option_11") + "</option>";
        options += '<option value="4106,4107,4108,4109,4110,4111">' + tcg.lib.prop("ac_income_option_12") + "</option>";
        if (parseInt(sessionStorage.isAgent) > 0) {
          options += '<option value="6114">' + tcg.lib.prop("ac_income_option_13") + "</option>";
        }
        options += '<option value="4280">' + tcg.lib.prop("ac_income_option_14") + "</option>";
        options += '<option value="4105">' + tcg.lib.prop("ac_income_option_15") + "</option>";
        //options += '<option value="4104">活动</option>';
        options += '<option value="4101">' + tcg.lib.prop("ac_income_option_2") + "</option>";
        options += "</optgroup>";

        options += '<optgroup label="' + tcg.lib.prop("ac_expenditure") + '">';
        options +=
          '<option value="4200">' + tcg.lib.prop("ac_subordinate_management_pvp_history_column_3") + "</option>";
        options += '<option value="4201">' + tcg.lib.prop("ac_income_option_2") + "</option>";
        options += '<option value="4208">' + tcg.lib.prop("ac_income_option_16") + "</option>";
        options += "</optgroup>";

        $(type).html(options);
        break;
      default:
    }

    $(type).trigger("chosen:updated");
  };

  // PNL Agent Report
  tcg.ac.ui.switchAgentPnl = function () {
    $(document)
      .off("click", "#switchAgentPnl li")
      .on("click", "#switchAgentPnl li", function () {
        var rel = $(this).attr("data-rel");
        if (!$(this).hasClass("active")) {
          if (rel == "lotto_rel") {
            $("#lottoAgentPnlContainer").removeClass("hide");
            $("#pvpAgentPnlContainer,#rngAgentPnlContainer,#fishAgentPnlContainer,#sportsAgentPnlContainer").addClass(
              "hide"
            );
            $("#lottoAgentPnlForm .form-submit").click();
          } else if (rel == "pvp_rel") {
            $("#pvpAgentPnlContainer").removeClass("hide");
            $("#lottoAgentPnlContainer,#rngAgentPnlContainer,#fishAgentPnlContainer,#sportsAgentPnlContainer").addClass(
              "hide"
            );
            $("#pvpAgentPnlForm .form-submit").click();
          } else if (rel == "rng_rel") {
            $("#rngAgentPnlContainer").removeClass("hide");
            $("#lottoAgentPnlContainer,#pvpAgentPnlContainer,#fishAgentPnlContainer,#sportsAgentPnlContainer").addClass(
              "hide"
            );
            $("#rngAgentPnlForm .form-submit").click();
          } else if (rel == "fish_rel") {
            $("#fishAgentPnlContainer").removeClass("hide");
            $("#lottoAgentPnlContainer,#pvpAgentPnlContainer,#rngAgentPnlContainer,#sportsAgentPnlContainer").addClass(
              "hide"
            );
            $("#fishAgentPnlForm .form-submit").click();
          } else if (rel == "sports_rel") {
            $("#sportsAgentPnlContainer").removeClass("hide");
            $("#lottoAgentPnlContainer,#pvpAgentPnlContainer,#rngAgentPnlContainer,#fishAgentPnlContainer").addClass(
              "hide"
            );
            $("#sportsAgentPnlForm .form-submit").click();
          }
          $("#switchAgentPnl li.active").removeClass("active");
          $(this).addClass("active");
        }
      });
  };
  tcg.ac.ui.switchAgentPnlCopy = function () {
    $(document)
      .off("click", "#switchAgentPnl li")
      .on("click", "#switchAgentPnl li", function () {
        var rel = $(this).attr("data-rel");
        if (!$(this).hasClass("active")) {
          $("div.palTab").addClass("hide");
          if (rel == "cons_rel") {
            $("#consAgentPnlContainer").removeClass("hide");
            $("#consAgentPnlForm .form-submit").click();
          } else if (rel == "lott_rel") {
            $("#lottoAgentPnlContainer").removeClass("hide");
            $("#lottoAgentPnlForm .form-submit").click();
          } else if (rel == "pvp_rel") {
            $("#pvpAgentPnlContainer").removeClass("hide");
            $("#pvpAgentPnlForm .form-submit").click();
          } else if (rel == "rng_rel") {
            $("#rngAgentPnlContainer").removeClass("hide");
            $("#rngAgentPnlForm .form-submit").click();
          } else if (rel == "live_rel") {
            $("#liveAgentPnlContainer").removeClass("hide");
            $("#liveAgentPnlForm .form-submit").click();
          } else if (rel == "fish_rel") {
            $("#fishAgentPnlContainer").removeClass("hide");
            $("#fishAgentPnlForm .form-submit").click();
          } else if (rel == "sports_rel") {
            $("#sportsAgentPnlContainer").removeClass("hide");
            $("#sportsAgentPnlForm .form-submit").click();
          } else if (rel == "elott_rel") {
            $("#elottAgentPnlContainer").removeClass("hide");
            $("#elottAgentPnlForm .form-submit").click();
          }
          $("#switchAgentPnl li.active").removeClass("active");
          $(this).addClass("active");
        }
      });
  };
  // Cons PNLCopy Functions
  tcg.ac.ui.searchConsAgentPnl = function () {
    $(document)
      .off("click", "#consAgentPnlForm .form-submit")
      .on("click", "#consAgentPnlForm .form-submit", function (event) {
        $(".switchDecimal").removeClass("on").removeClass("off");
        if (event.originalEvent === undefined) {
          var len = $("#consAgentPnlForm .b-list").length - 1;
          var customerId = $($("#consAgentPnlForm .b-list")[len]).attr("data-customerid");
          if ($("#consAgentPnlTable .divTableCell.sort").hasClass("asc")) {
            var type = $("#consAgentPnlTable .divTableCell.sort.asc").attr("data-type");
            tcg.ac.getConsAgentPnl(customerId, type, "asc");
          } else if ($("#consAgentPnlTable .divTableCell.sort").hasClass("desc")) {
            var type = $("#consAgentPnlTable .divTableCell.sort.desc").attr("data-type");
            tcg.ac.getConsAgentPnl(customerId, type, "desc");
          } else {
            tcg.ac.getConsAgentPnl(customerId);
          }
        } else {
          $("#consAgentPnlForm .breadcrumbs .b-list.new-entry").remove();
          tcg.ac.getConsAgentPnl();
        }
      });
  };
  tcg.ac.ui.loadConsAgentPnl = function (result, summaryType, sortType, sortDir) {
    var _column = "";
    var currentUser = {};
    for (var i = 0; i < result.list.length; i++) {
      if (tcg.ac.customerId === +result.list[i].customerId) {
        currentUser = result.list[i];
        break;
      }
    }
    if (summaryType == 0) {
      _column += "<div class='divTableCell '>" + tcg.lib.prop("ac_lotto_pnl_summaryType_0_column_1") + "</div>";
      _column +=
        "<div class='divTableCell tbl-link sort' data-sort='' data-type='deposit'>" +
        tcg.lib.prop("ac_lotto_pnl_summaryType_0_column_2") +
        "</div>";
      _column +=
        "<div class='divTableCell tbl-link sort' data-sort='' data-type='withdraw'>" +
        tcg.lib.prop("ac_lotto_pnl_summaryType_0_column_3") +
        "</div>";
      _column +=
        "<div class='divTableCell tbl-link sort' data-sort='' data-type='betAmount'>" +
        tcg.lib.prop("ac_lotto_pnl_summaryType_0_column_4") +
        "</div>";
      _column +=
        "<div class='divTableCell tbl-link sort' data-sort='' data-type='winAmount'>" +
        tcg.lib.prop("ac_lotto_pnl_summaryType_0_column_6") +
        "</div>";
      _column += "<div class='divTableCell '>" + tcg.lib.prop("ac_lotto_pnl_summaryType_0_column_5") + "</div>";
      if (sessionStorage.enableSalary == "true") {
        _column += "<div class='divTableCell '>" + tcg.lib.prop("ac_lotto_pnl_summaryType_0_column_9") + "</div>";
      }
      _column += "<div class='divTableCell '>" + tcg.lib.prop("ac_lotto_pnl_summaryType_0_column_7") + "</div>";
      if (currentUser.bankCharges != undefined && currentUser.bankCharges != 0) {
        _column +=
          "<div class='divTableCell tbl-link' data-sort='' data-type='bankCharges'>" +
          tcg.lib.prop("ac_lotto_pnl_summaryType_0_column_10") +
          "</div>";
      }
      if (currentUser.merchantCharge != undefined && currentUser.merchantCharge != 0) {
        _column +=
          "<div class='divTableCell tbl-link' data-sort='' data-type='merchantCharge'>" +
          tcg.lib.prop("ac_lotto_pnl_summaryType_0_column_11") +
          "</div>";
      }
      _column +=
        "<div class='divTableCell tbl-link sort' data-sort='' data-type='profitAndLoss'>" +
        tcg.lib.prop("ac_lotto_pnl_summaryType_0_column_8") +
        "</div>";
    } else {
      _column += "<div class='divTableCell '>" + tcg.lib.prop("ac_lotto_pnl_summaryType_1_column_1") + "</div>";
      _column +=
        "<div class='divTableCell tbl-link sort' data-sort='' data-type='deposit'>" +
        tcg.lib.prop("ac_lotto_pnl_summaryType_1_column_2") +
        "</div>";
      _column +=
        "<div class='divTableCell tbl-link sort' data-sort='' data-type='withdraw'>" +
        tcg.lib.prop("ac_lotto_pnl_summaryType_1_column_3") +
        "</div>";
      _column +=
        "<div class='divTableCell tbl-link sort' data-sort='' data-type='betAmount'>" +
        tcg.lib.prop("ac_lotto_pnl_summaryType_1_column_4") +
        "</div>";
      _column +=
        "<div class='divTableCell tbl-link sort' data-sort='' data-type='winAmount'>" +
        tcg.lib.prop("ac_lotto_pnl_summaryType_1_column_6") +
        "</div>";
      _column += "<div class='divTableCell '>" + tcg.lib.prop("ac_lotto_pnl_summaryType_1_column_5") + "</div>";
      if (sessionStorage.enableSalary == "true") {
        _column += "<div class='divTableCell '>" + tcg.lib.prop("ac_lotto_pnl_summaryType_1_column_9") + "</div>";
      }
      _column += "<div class='divTableCell '>" + tcg.lib.prop("ac_lotto_pnl_summaryType_1_column_7") + "</div>";
      if (currentUser.bankCharges != undefined && currentUser.bankCharges != 0) {
        _column +=
          "<div class='divTableCell tbl-link' data-sort='' data-type='bankCharges'>" +
          tcg.lib.prop("ac_lotto_pnl_summaryType_1_column_10") +
          "</div>";
      }
      if (currentUser.merchantCharge != undefined && currentUser.merchantCharge != 0) {
        _column +=
          "<div class='divTableCell tbl-link' data-sort='' data-type='merchantCharge'>" +
          tcg.lib.prop("ac_lotto_pnl_summaryType_1_column_11") +
          "</div>";
      }
      _column +=
        "<div class='divTableCell tbl-link sort' data-sort='' data-type='profitAndLoss'>" +
        tcg.lib.prop("ac_lotto_pnl_summaryType_1_column_8") +
        "</div>";
    }
    $("#consAgentPnlColumn").html(_column);
    var decimal = 2,
      list = result.list,
      _html = "";
    for (var i = 0; i < list.length; i++) {
      _html += "<div class='divTableRow'>";
      var account = list[i].customerName;
      _html += "<div class='divTableCell tbl-link' data-customerId='" + account + "'>" + account + "</div>";
      var depositAndTransferIn = list[i].deposit;
      _html +=
        "<div class='divTableCell' data-switchDecimal='" +
        decimal +
        "' data-value='" +
        list[i].deposit +
        "'>" +
        tcg.ac.customCurrencyFormat(depositAndTransferIn, decimal) +
        "</div>";
      var withdrawAndTransferOut = list[i].withdraw;
      _html +=
        "<div class='divTableCell' data-switchDecimal='" +
        decimal +
        "' data-value='" +
        list[i].withdraw +
        "'>" +
        tcg.ac.customCurrencyFormat(withdrawAndTransferOut, decimal) +
        "</div>";
      _html +=
        "<div class='divTableCell' data-switchDecimal='" +
        decimal +
        "' data-value='" +
        list[i].betAmount +
        "'>" +
        tcg.ac.customCurrencyFormat(list[i].betAmount, decimal) +
        "</div>";
      _html +=
        "<div class='divTableCell' data-switchDecimal='" +
        decimal +
        "' data-value='" +
        list[i].winAmount +
        "'>" +
        tcg.ac.customCurrencyFormat(list[i].winAmount, decimal) +
        "</div>";
      var consGameRebate = list[i].agentCommission + list[i].rebate;
      var lottGameRebateColor = consGameRebate;
      _html +=
        "<div class='divTableCell " +
        lottGameRebateColor +
        "'  data-switchDecimal='" +
        decimal +
        "' data-value='" +
        consGameRebate +
        "'>" +
        tcg.ac.customCurrencyFormat(consGameRebate, decimal) +
        "</div>";
      if (sessionStorage.enableSalary == "true") {
        _html +=
          "<div class='divTableCell' data-switchDecimal='" +
          decimal +
          "' data-value='" +
          list[i].dailyIncome +
          "'>" +
          tcg.ac.customCurrencyFormat(list[i].dailyIncome, decimal) +
          "</div>";
      }
      var promobrokeincome = list[i].profitSharing + list[i].promotion;
      _html +=
        "<div class='divTableCell' data-switchDecimal='" +
        decimal +
        "' data-value='" +
        promobrokeincome +
        "'>" +
        tcg.ac.customCurrencyFormat(promobrokeincome, decimal) +
        "</div>";
      if (currentUser.bankCharges != undefined && currentUser.bankCharges != 0) {
        _html +=
          "<div class='divTableCell' data-switchDecimal='" +
          decimal +
          "' data-value='" +
          list[i].bankCharges +
          "'>" +
          tcg.ac.customCurrencyFormat(list[i].bankCharges, decimal) +
          "</div>";
      }
      if (currentUser.merchantCharge != undefined && currentUser.merchantCharge != 0) {
        _html +=
          "<div class='divTableCell' data-switchDecimal='" +
          decimal +
          "' data-value='" +
          list[i].merchantCharge +
          "'>" +
          tcg.ac.customCurrencyFormat(list[i].merchantCharge, decimal) +
          "</div>";
      }
      var profitLossColor = list[i].profitAndLoss >= 0 ? "tbl-red" : "tbl-green";
      _html +=
        "<div class='divTableCell " +
        profitLossColor +
        "' data-switchDecimal='" +
        decimal +
        "' data-value='" +
        list[i].profitAndLoss +
        "'>" +
        tcg.ac.customCurrencyFormat(list[i].profitAndLoss, decimal) +
        "</div>";
      _html += "</div>";
    }
    $("#consAgentPnlList").html(_html);
    if (sortType && sortDir) {
      $("#consAgentPnlTable .divTableCell.tbl-link.sort[data-type='" + sortType + "']")
        .attr("data-sort", sortDir)
        .addClass(sortDir);
    }
  };
  tcg.ac.ui.switchConsAgentPnlSummaryType = function () {
    $(document)
      .off("click", "#consAgentPnlForm .summaryTypeList li")
      .on("click", "#consAgentPnlForm .summaryTypeList li", function () {
        if (!$(this).hasClass("active") && !$("#consAgentPnlForm .form-submit").hasClass("processing")) {
          var rel = $(this).attr("data-rel");
          $("#consAgentPnlForm .summaryTypeList li").removeClass("active");
          $(this).addClass("active");
          $("#consAgentPnlTable .divTableCell.sort").attr("data-sort", "").removeClass("asc").removeClass("desc");
          $("#consAgentPnlForm [name='pageNo']").val(1);
          $("#consAgentPnlForm [name='summaryType']").val(rel);
          $("#consAgentPnlForm .form-submit").click();
        }
      });
  };
  tcg.ac.ui.selectConsAgentDownlinePnl = function () {
    $(document)
      .off("click", "#consAgentPnlList .tbl-link")
      .on("click", "#consAgentPnlList .tbl-link", function () {
        if (!$(this).hasClass("loading")) {
          var customerName = $(this).text(),
            customerId = $(this).attr("data-customerId"),
            isOpen = false;
          $("#consAgentPnlForm .breadcrumbs .b-list").each(function () {
            if ($(this).attr("data-customerId") == customerId) {
              isOpen = true;
            }
          });
          if (!isOpen) {
            $("#consAgentPnlForm .breadcrumbs .b-list").addClass("loading");
            $("#consAgentPnlForm .summaryTypeList li").addClass("loading");
            _html =
              "<div class='arrow-small-con mem-icon2 inline-block b-list new-entry' data-customerId='" +
              customerId +
              "'>" +
              customerName +
              "</div>";
            $("#consAgentPnlForm .breadcrumbs").append(_html);
          }
          $("#consAgentPnlTable .divTableCell.sort").attr("data-sort", "").removeClass("asc").removeClass("desc");
          $("#consAgentPnlForm [name='pageNo']").val(1);
          tcg.ac.getConsAgentPnl(customerId);
        }
      });
  };
  tcg.ac.ui.clickConsAgentPnlBreadcrumbs = function () {
    $(document)
      .off("click", "#consAgentPnlForm .breadcrumbs .b-list")
      .on("click", "#consAgentPnlForm .breadcrumbs .b-list", function () {
        if (!$(this).hasClass("loading")) {
          var customerId = $(this).attr("data-customerId"),
            customerName = $(this).text(),
            removeFromList = false;
          $("#consAgentPnlForm .breadcrumbs .b-list").each(function () {
            if (removeFromList) $(this).remove();
            if ($(this).attr("data-customerId") == customerId) {
              removeFromList = true;
            }
          });
          $("#consAgentPnlTable .divTableCell.sort").attr("data-sort", "").removeClass("asc").removeClass("desc");
          $("#consAgentPnlForm [name='pageNo']").val(1);
          $("#consAgentPnlForm .breadcrumbs .b-list").addClass("loading");
          $("#consAgentPnlForm .summaryTypeList li").addClass("loading");
          tcg.ac.getConsAgentPnl(customerId);
        }
      });
  };
  tcg.ac.ui.sortConsAgentPnl = function () {
    $(document)
      .off("click", "#consAgentPnlTable .divTableCell.sort")
      .on("click", "#consAgentPnlTable .divTableCell.sort", function (event) {
        var type = $(this).attr("data-type");
        var sort = $(this).attr("data-sort");
        $("#consAgentPnlTable .divTableCell.sort").addClass("loading");
        $("#consAgentPnlTable .divTableCell.sort").removeClass("asc").removeClass("desc");
        $("#consAgentPnlTable .divTableCell.sort").attr("data-sort", "");
        if (sort === "") {
          $(this).attr("data-sort", "desc");
          sort = "desc";
        } else {
          if (sort == "desc") {
            $(this).attr("data-sort", "asc");
            sort = "asc";
          } else {
            $(this).attr("data-sort", "desc");
            sort = "desc";
          }
        }
        $(this).addClass(sort);
        console.log(type, sort);
        $("#consAgentPnlForm input[name='pageNo']").val(1);
        var len = $("#consAgentPnlForm .b-list").length - 1;
        var customerId = $($("#consAgentPnlForm .b-list")[len]).attr("data-customerid");
        tcg.ac.getConsAgentPnl(customerId, type, sort);
      });
  };
  // LottoCopy PNL Functions
  tcg.ac.ui.searchLottoCopyAgentPnl = function () {
    $(document)
      .off("click", "#lottoAgentPnlForm .form-submit")
      .on("click", "#lottoAgentPnlForm .form-submit", function (event) {
        $(".switchDecimal").removeClass("on").removeClass("off");
        if (event.originalEvent === undefined) {
          var len = $("#lottoAgentPnlForm .b-list").length - 1;
          var customerId = $($("#lottoAgentPnlForm .b-list")[len]).attr("data-customerid");
          if ($("#lottoAgentPnlTable .divTableCell.sort").hasClass("asc")) {
            var type = $("#lottoAgentPnlTable .divTableCell.sort.asc").attr("data-type");
            tcg.ac.getLottoCopyAgentPnl(customerId, type, "asc");
          } else if ($("#lottoAgentPnlTable .divTableCell.sort").hasClass("desc")) {
            var type = $("#lottoAgentPnlTable .divTableCell.sort.desc").attr("data-type");
            tcg.ac.getLottoCopyAgentPnl(customerId, type, "desc");
          } else {
            tcg.ac.getLottoCopyAgentPnl(customerId);
          }
        } else {
          $("#lottoAgentPnlForm .breadcrumbs .b-list.new-entry").remove();
          tcg.ac.getLottoCopyAgentPnl();
        }
      });
  };
  tcg.ac.ui.loadLottoCopyAgentPnl = function (result, summaryType, sortType, sortDir) {
    var _column = "";
    var currentUser = {};
    for (var i = 0; i < result.list.length; i++) {
      if (tcg.ac.customerId === +result.list[i].customerId) {
        currentUser = result.list[i];
        break;
      }
    }
    if (summaryType == 0) {
      _column += "<div class='divTableCell'>" + tcg.lib.prop("ac_lotto_pnl_summaryType_0_column_1") + "</div>";
      // _column += "<div class='divTableCell'>" +tcg.lib.prop("ac_lotto_pnl_summaryType_0_column_2")+ "</div>";
      // _column += "<div class='divTableCell'>" +tcg.lib.prop("ac_lotto_pnl_summaryType_0_column_3")+ "</div>";
      _column +=
        "<div class='divTableCell tbl-link sort' data-sort='' data-type='betAmount'>" +
        tcg.lib.prop("ac_lotto_pnl_summaryType_0_column_4") +
        "</div>";
      _column +=
        "<div class='divTableCell tbl-link sort' data-sort='' data-type='winAmount'>" +
        tcg.lib.prop("ac_lotto_pnl_summaryType_0_column_6") +
        "</div>";
      _column += "<div class='divTableCell'>" + tcg.lib.prop("ac_lotto_pnl_summaryType_0_column_5") + "</div>";
      if (sessionStorage.enableSalary == "true") {
        _column += "<div class='divTableCell'>" + tcg.lib.prop("ac_lotto_pnl_summaryType_0_column_9") + "</div>";
      }
      _column += "<div class='divTableCell'>" + tcg.lib.prop("ac_lotto_pnl_summaryType_0_column_7") + "</div>";
      if (currentUser.merchantCharge != undefined && currentUser.merchantCharge != 0) {
        _column +=
          "<div class='divTableCell tbl-link' data-sort='' data-type='merchantCharge'>" +
          tcg.lib.prop("ac_lotto_pnl_summaryType_0_column_11") +
          "</div>";
      }
      _column +=
        "<div class='divTableCell tbl-link sort' data-sort='' data-type='profitAndLoss'>" +
        tcg.lib.prop("ac_lotto_pnl_summaryType_0_column_8") +
        "</div>";
    } else {
      _column += "<div class='divTableCell'>" + tcg.lib.prop("ac_lotto_pnl_summaryType_1_column_1") + "</div>";
      // _column += "<div class='divTableCell'>" +tcg.lib.prop("ac_lotto_pnl_summaryType_1_column_2")+ "</div>";
      // _column += "<div class='divTableCell'>" +tcg.lib.prop("ac_lotto_pnl_summaryType_1_column_3")+ "</div>";
      _column +=
        "<div class='divTableCell tbl-link sort' data-sort='' data-type='betAmount'>" +
        tcg.lib.prop("ac_lotto_pnl_summaryType_1_column_4") +
        "</div>";
      _column +=
        "<div class='divTableCell tbl-link sort' data-sort='' data-type='winAmount'>" +
        tcg.lib.prop("ac_lotto_pnl_summaryType_1_column_6") +
        "</div>";
      _column += "<div class='divTableCell'>" + tcg.lib.prop("ac_lotto_pnl_summaryType_1_column_5") + "</div>";
      if (sessionStorage.enableSalary == "true") {
        _column += "<div class='divTableCell'>" + tcg.lib.prop("ac_lotto_pnl_summaryType_1_column_9") + "</div>";
      }
      _column += "<div class='divTableCell'>" + tcg.lib.prop("ac_lotto_pnl_summaryType_1_column_7") + "</div>";
      if (currentUser.merchantCharge != undefined && currentUser.merchantCharge != 0) {
        _column +=
          "<div class='divTableCell tbl-link' data-sort='' data-type='merchantCharge'>" +
          tcg.lib.prop("ac_lotto_pnl_summaryType_1_column_11") +
          "</div>";
      }
      _column +=
        "<div class='divTableCell tbl-link sort' data-sort='' data-type='profitAndLoss'>" +
        tcg.lib.prop("ac_lotto_pnl_summaryType_1_column_8") +
        "</div>";
    }
    $("#lottoAgentPnlColumn").html(_column);

    var decimal = 2,
      list = result.list,
      _html = "";
    for (var i = 0; i < list.length; i++) {
      _html += "<div class='divTableRow'>";
      var account = list[i].customerName;
      _html += "<div class='divTableCell tbl-link' data-customerId='" + account + "'>" + account + "</div>";
      // var depositAndTransferIn = list[i].deposit + list[i].agent_transfer_in;
      // _html += "<div class='divTableCell ps-num' data-switchDecimal='" + decimal + "' data-value='" + list[i].deposit + "'>" + tcg.ac.customCurrencyFormat(depositAndTransferIn, decimal) + "</div>";
      // var withdrawAndTransferOut = list[i].withdraw + list[i].agent_transfer_out;
      // _html += "<div class='divTableCell ps-num' data-switchDecimal='" + decimal + "' data-value='" + list[i].withdraw + "'>" + tcg.ac.customCurrencyFormat(withdrawAndTransferOut, decimal) + "</div>";
      _html +=
        "<div class='divTableCell' data-switchDecimal='" +
        decimal +
        "' data-value='" +
        list[i].betAmount +
        "'>" +
        tcg.ac.customCurrencyFormat(list[i].betAmount, decimal) +
        "</div>";
      _html +=
        "<div class='divTableCell' data-switchDecimal='" +
        decimal +
        "' data-value='" +
        list[i].winAmount +
        "'>" +
        tcg.ac.customCurrencyFormat(list[i].winAmount, decimal) +
        "</div>";
      var lottoGameRebate = list[i].agentCommission + list[i].rebate;
      var lottGameRebateColor = lottoGameRebate;
      _html +=
        "<div class='divTableCell " +
        lottGameRebateColor +
        "'  data-switchDecimal='" +
        decimal +
        "' data-value='" +
        lottoGameRebate +
        "'>" +
        tcg.ac.customCurrencyFormat(lottoGameRebate, decimal) +
        "</div>";

      if (sessionStorage.enableSalary == "true") {
        _html +=
          "<div class='divTableCell' data-switchDecimal='" +
          decimal +
          "' data-value='" +
          list[i].dailyIncome +
          "'>" +
          tcg.ac.customCurrencyFormat(list[i].dailyIncome, decimal) +
          "</div>";
      }
      var promobrokeincome = list[i].promotion;
      //var promobrokeincome = list[i].dailyIncome + list[i].promotion;
      _html +=
        "<div class='divTableCell' data-switchDecimal='" +
        decimal +
        "' data-value='" +
        promobrokeincome +
        "'>" +
        tcg.ac.customCurrencyFormat(promobrokeincome, decimal) +
        "</div>";
      if (currentUser.merchantCharge != undefined && currentUser.merchantCharge != 0) {
        _html +=
          "<div class='divTableCell' data-switchDecimal='" +
          decimal +
          "' data-value='" +
          list[i].merchantCharge +
          "'>" +
          tcg.ac.customCurrencyFormat(list[i].merchantCharge, decimal) +
          "</div>";
      }
      var profitLossColor = list[i].profitAndLoss >= 0 ? "tbl-red" : "tbl-green";
      _html +=
        "<div class='divTableCell " +
        profitLossColor +
        "' data-switchDecimal='" +
        decimal +
        "' data-value='" +
        list[i].profitAndLoss +
        "'>" +
        tcg.ac.customCurrencyFormat(list[i].profitAndLoss, decimal) +
        "</div>";
      _html += "</div>";
    }
    $("#lottoAgentPnlList").html(_html);
    if (sortType && sortDir) {
      $("#lottoAgentPnlTable .divTableCell.tbl-link.sort[data-type='" + sortType + "']")
        .attr("data-sort", sortDir)
        .addClass(sortDir);
    }
  };
  tcg.ac.ui.switchLottoCopyAgentPnlSummaryType = function () {
    $(document)
      .off("click", "#lottoAgentPnlForm .summaryTypeList li")
      .on("click", "#lottoAgentPnlForm .summaryTypeList li", function () {
        if (!$(this).hasClass("active") && !$("#lottoAgentPnlForm .form-submit").hasClass("processing")) {
          var rel = $(this).attr("data-rel");
          $("#lottoAgentPnlForm .summaryTypeList li").removeClass("active");
          $(this).addClass("active");
          $("#lottoAgentPnlTable .divTableCell.sort").attr("data-sort", "").removeClass("asc").removeClass("desc");
          $("#lottoAgentPnlForm [name='pageNo']").val(1);
          $("#lottoAgentPnlForm [name='summaryType']").val(rel);
          $("#lottoAgentPnlForm .form-submit").click();
        }
      });
  };
  tcg.ac.ui.selectLottoCopyAgentDownlinePnl = function () {
    $(document)
      .off("click", "#lottoAgentPnlList .tbl-link")
      .on("click", "#lottoAgentPnlList .tbl-link", function () {
        if (!$(this).hasClass("loading")) {
          var customerName = $(this).text(),
            customerId = $(this).attr("data-customerId"),
            isOpen = false;
          $("#lottoAgentPnlForm .breadcrumbs .b-list").each(function () {
            if ($(this).attr("data-customerId") == customerId) {
              isOpen = true;
            }
          });
          if (!isOpen) {
            $("#lottoAgentPnlForm .breadcrumbs .b-list").addClass("loading");
            $("#lottoAgentPnlForm .summaryTypeList li").addClass("loading");
            _html =
              "<div class='arrow-small-con mem-icon2 inline-block b-list new-entry' data-customerId='" +
              customerId +
              "'>" +
              customerName +
              "</div>";
            $("#lottoAgentPnlForm .breadcrumbs").append(_html);
          }
          $("#lottoAgentPnlTable .divTableCell.sort").attr("data-sort", "").removeClass("asc").removeClass("desc");
          $("#lottoAgentPnlForm [name='pageNo']").val(1);
          tcg.ac.getLottoCopyAgentPnl(customerId);
        }
      });
  };
  tcg.ac.ui.clickLottoCopyAgentPnlBreadcrumbs = function () {
    $(document)
      .off("click", "#lottoAgentPnlForm .breadcrumbs .b-list")
      .on("click", "#lottoAgentPnlForm .breadcrumbs .b-list", function () {
        if (!$(this).hasClass("loading")) {
          var customerId = $(this).attr("data-customerId"),
            customerName = $(this).text(),
            removeFromList = false;
          $("#lottoAgentPnlForm .breadcrumbs .b-list").each(function () {
            if (removeFromList) $(this).remove();
            if ($(this).attr("data-customerId") == customerId) {
              removeFromList = true;
            }
          });
          $("#lottoAgentPnlTable .divTableCell.sort").attr("data-sort", "").removeClass("asc").removeClass("desc");
          $("#lottoAgentPnlForm [name='pageNo']").val(1);
          $("#lottoAgentPnlForm .breadcrumbs .b-list").addClass("loading");
          $("#lottoAgentPnlForm .summaryTypeList li").addClass("loading");
          tcg.ac.getLottoCopyAgentPnl(customerId);
        }
      });
  };
  tcg.ac.ui.sortLottoCopyAgentPnl = function () {
    $(document)
      .off("click", "#lottoAgentPnlTable .divTableCell.sort")
      .on("click", "#lottoAgentPnlTable .divTableCell.sort", function (event) {
        var type = $(this).attr("data-type");
        var sort = $(this).attr("data-sort");
        $("#lottoAgentPnlTable .divTableCell.sort").addClass("loading");
        $("#lottoAgentPnlTable .divTableCell.sort").removeClass("asc").removeClass("desc");
        $("#lottoAgentPnlTable .divTableCell.sort").attr("data-sort", "");
        if (sort === "") {
          $(this).attr("data-sort", "desc");
          sort = "desc";
        } else {
          if (sort == "desc") {
            $(this).attr("data-sort", "asc");
            sort = "asc";
          } else {
            $(this).attr("data-sort", "desc");
            sort = "desc";
          }
        }
        $(this).addClass(sort);
        console.log(type, sort);
        $("#lottoAgentPnlForm input[name='pageNo']").val(1);
        var len = $("#lottoAgentPnlForm .b-list").length - 1;
        var customerId = $($("#lottoAgentPnlForm .b-list")[len]).attr("data-customerid");
        tcg.ac.getLottoCopyAgentPnl(customerId, type, sort);
      });
  };
  tcg.ac.ui.searchPvpCopyAgentPnl = function () {
    $(document)
      .off("click", "#pvpAgentPnlForm .form-submit")
      .on("click", "#pvpAgentPnlForm .form-submit", function (event) {
        $(".switchDecimal").removeClass("on").removeClass("off");
        if (event.originalEvent === undefined) {
          var len = $("#pvpAgentPnlForm .b-list").length;
          var customerId = $($("#pvpAgentPnlForm .b-list")[len]).attr("data-customerid");
          if ($("#pvpAgentPnlTable .divTableCell.sort").hasClass("asc")) {
            var type = $("#pvpAgentPnlTable .divTableCell.sort.asc").attr("data-type");
            tcg.ac.getPvpCopyAgentPnl(customerId, type, "asc");
          } else if ($("#pvpAgentPnlTable .divTableCell.sort").hasClass("desc")) {
            var type = $("#pvpAgentPnlTable .divTableCell.sort.desc").attr("data-type");
            tcg.ac.getPvpCopyAgentPnl(customerId, type, "desc");
          } else {
            tcg.ac.getPvpCopyAgentPnl(customerId);
          }
        } else {
          $("#pvpAgentPnlForm .breadcrumbs .b-list.new-entry").remove();
          tcg.ac.getPvpCopyAgentPnl();
        }
      });
  };
  tcg.ac.ui.loadPvpCopyAgentPnl = function (result, summaryType, sortType, sortDir) {
    var _column = "";
    var currentUser = {};
    for (var i = 0; i < result.list.length; i++) {
      if (tcg.ac.customerId === +result.list[i].customerId) {
        currentUser = result.list[i];
        break;
      }
    }
    if (summaryType == 0) {
      _column += "<div class='divTableCell'>" + tcg.lib.prop("ac_pvp_pnl_summaryType_0_column_1") + "</div>";
      // _column += "<div class='divTableCell'>" +tcg.lib.prop("ac_pvp_pnl_summaryType_0_column_2")+ "</div>";
      // _column += "<div class='divTableCell'>" +tcg.lib.prop("ac_pvp_pnl_summaryType_0_column_3")+ "</div>";
      _column +=
        "<div class='divTableCell tbl-link sort' data-sort='' data-type='loseAmount'>" +
        tcg.lib.prop("ac_pvp_pnl_summaryType_0_column_4") +
        "</div>";
      _column +=
        "<div class='divTableCell tbl-link sort' data-sort='' data-type='winAmount'>" +
        tcg.lib.prop("ac_pvp_pnl_summaryType_0_column_5") +
        "</div>";
      _column += "<div class='divTableCell'>" + tcg.lib.prop("ac_pvp_pnl_summaryType_0_column_6") + "</div>";
      _column += "<div class='divTableCell'>" + tcg.lib.prop("ac_pvp_pnl_summaryType_0_column_7") + "</div>";
      if (currentUser.merchantCharge != undefined && currentUser.merchantCharge != 0) {
        _column +=
          "<div class='divTableCell tbl-link' data-sort='' data-type='merchantCharge'>" +
          tcg.lib.prop("ac_lotto_pnl_summaryType_0_column_11") +
          "</div>";
      }
      _column +=
        "<div class='divTableCell tbl-link sort' data-sort='' data-type='profitAndLoss'>" +
        tcg.lib.prop("ac_pvp_pnl_summaryType_0_column_8") +
        "</div>";
    } else {
      _column += "<div class='divTableCell'>" + tcg.lib.prop("ac_pvp_pnl_summaryType_1_column_1") + "</div>";
      // _column += "<div class='divTableCell'>" +tcg.lib.prop("ac_pvp_pnl_summaryType_1_column_2")+ "</div>";
      // _column += "<div class='divTableCell'>" +tcg.lib.prop("ac_pvp_pnl_summaryType_1_column_3")+ "</div>";
      _column +=
        "<div class='divTableCell tbl-link sort' data-sort='' data-type='loseAmount'>" +
        tcg.lib.prop("ac_pvp_pnl_summaryType_1_column_4") +
        "</div>";
      _column +=
        "<div class='divTableCell tbl-link sort' data-sort='' data-type='winAmount'>" +
        tcg.lib.prop("ac_pvp_pnl_summaryType_1_column_5") +
        "</div>";
      _column += "<div class='divTableCell'>" + tcg.lib.prop("ac_pvp_pnl_summaryType_1_column_6") + "</div>";
      _column += "<div class='divTableCell'>" + tcg.lib.prop("ac_pvp_pnl_summaryType_1_column_7") + "</div>";
      if (currentUser.merchantCharge != undefined && currentUser.merchantCharge != 0) {
        _column +=
          "<div class='divTableCell tbl-link' data-sort='' data-type='merchantCharge'>" +
          tcg.lib.prop("ac_lotto_pnl_summaryType_1_column_11") +
          "</div>";
      }
      _column +=
        "<div class='divTableCell tbl-link sort' data-sort='' data-type='profitAndLoss'>" +
        tcg.lib.prop("ac_pvp_pnl_summaryType_1_column_8") +
        "</div>";
    }
    $("#pvpAgentPnlColumn").html(_column);

    var decimal = 2,
      list = result.list,
      _html = "";
    if (list.length > 0) {
      for (var i = 0; i < list.length; i++) {
        _html += "<div class='divTableRow'>";
        var account = list[i].customerName;
        _html += "<div class='divTableCell tbl-link' data-customerId='" + account + "'>" + account + "</div>";
        // var total_deposit = list[i].deposit;
        // _html += "<div class='divTableCell ps-num' data-switchDecimal='" + decimal + "' data-value='" + total_deposit + "'>" + tcg.ac.customCurrencyFormat(total_deposit, decimal) + "</div>";
        // var total_withdraw = list[i].withdraw;
        // _html += "<div class='divTableCell ps-num' data-switchDecimal='" + decimal + "' data-value='" + total_withdraw + "'>" + tcg.ac.customCurrencyFormat(total_withdraw, decimal) + "</div>";
        var total_betting_loss = list[i].loseAmount;
        _html +=
          "<div class='divTableCell' data-switchDecimal='" +
          decimal +
          "' data-value='" +
          total_betting_loss +
          "'>" +
          tcg.ac.customCurrencyFormat(total_betting_loss, decimal) +
          "</div>";
        var total_game_winnings = list[i].winAmount;
        var gameWinningColor = total_game_winnings;
        _html +=
          "<div class='divTableCell " +
          gameWinningColor +
          "'  data-switchDecimal='" +
          decimal +
          "' data-value='" +
          total_game_winnings +
          "'>" +
          tcg.ac.customCurrencyFormat(total_game_winnings, decimal) +
          "</div>";
        var total_game_rebate = list[i].agentCommission + list[i].rebate;
        _html +=
          "<div class='divTableCell' data-switchDecimal='" +
          decimal +
          "' data-value='" +
          total_game_rebate +
          "'>" +
          tcg.ac.customCurrencyFormat(total_game_rebate, decimal) +
          "</div>";
        var total_promotion = list[i].promotion;
        _html +=
          "<div class='divTableCell' data-switchDecimal='" +
          decimal +
          "' data-value='" +
          total_promotion +
          "'>" +
          tcg.ac.customCurrencyFormat(total_promotion, decimal) +
          "</div>";
        if (currentUser.merchantCharge != undefined && currentUser.merchantCharge != 0) {
          _html +=
            "<div class='divTableCell' data-switchDecimal='" +
            decimal +
            "' data-value='" +
            list[i].merchantCharge +
            "'>" +
            tcg.ac.customCurrencyFormat(list[i].merchantCharge, decimal) +
            "</div>";
        }
        var total_pnl = list[i].profitAndLoss;
        var profitLossColor = total_pnl >= 0 ? "tbl-red" : "tbl-green";
        _html +=
          "<div class='divTableCell " +
          profitLossColor +
          "' data-switchDecimal='" +
          decimal +
          "' data-value='" +
          total_pnl +
          "'>" +
          tcg.ac.customCurrencyFormat(total_pnl, decimal) +
          "</div>";
        _html += "</div>";
      }
    } else {
      _html += "<div class='tableContent-wrp'>";
      _html += "<div class='noResult-data'>" + tcg.lib.prop("no_result") + "</div>";
      _html += "</div>";
    }
    $("#pvpAgentPnlList").html(_html);
    if (sortType && sortDir) {
      $("#pvpAgentPnlTable .divTableCell.tbl-link.sort[data-type='" + sortType + "']")
        .attr("data-sort", sortDir)
        .addClass(sortDir);
    }
  };
  tcg.ac.ui.switchPvpCopyAgentPnlSummaryType = function () {
    $(document)
      .off("click", "#pvpAgentPnlForm .summaryTypeList li")
      .on("click", "#pvpAgentPnlForm .summaryTypeList li", function () {
        if (!$(this).hasClass("active") && !$("#pvpAgentPnlForm .form-submit").hasClass("processing")) {
          var rel = $(this).attr("data-rel");
          $("#pvpAgentPnlForm .summaryTypeList li").removeClass("active");
          $(this).addClass("active");
          $("#pvpAgentPnlTable .divTableCell.sort").attr("data-sort", "").removeClass("asc").removeClass("desc");
          $("#pvpAgentPnlForm [name='pageNo']").val(1);
          $("#pvpAgentPnlForm [name='summaryType']").val(rel);
          $("#pvpAgentPnlForm .form-submit").click();
        }
      });
  };
  tcg.ac.ui.selectPvpCopyAgentDownlinePnl = function () {
    $(document)
      .off("click", "#pvpAgentPnlList .tbl-link")
      .on("click", "#pvpAgentPnlList .tbl-link", function () {
        if (!$(this).hasClass("loading")) {
          var customerName = $(this).text(),
            customerId = $(this).attr("data-customerId"),
            isOpen = false;
          $("#pvpAgentPnlForm .breadcrumbs .b-list").each(function () {
            if ($(this).attr("data-customerId") == customerId) {
              isOpen = true;
            }
          });
          if (!isOpen) {
            $("#pvpAgentPnlForm .breadcrumbs .b-list").addClass("loading");
            $("#pvpAgentPnlList .tbl-link").addClass("loading");
            var _html =
              "<div class='arrow-small-con mem-icon2 inline-block b-list new-entry' data-customerId='" +
              customerId +
              "'>" +
              customerName +
              "</div>";
            $("#pvpAgentPnlForm .breadcrumbs").append(_html);
          }
          $("#pvpAgentPnlTable .divTableCell.sort").attr("data-sort", "").removeClass("asc").removeClass("desc");
          $("#pvpAgentPnlForm [name='pageNo']").val(1);
          tcg.ac.getPvpCopyAgentPnl(customerId);
        }
      });
  };
  tcg.ac.ui.clickPvpCopyAgentPnlBreadcrumbs = function () {
    $(document)
      .off("click", "#pvpAgentPnlForm .breadcrumbs .b-list")
      .on("click", "#pvpAgentPnlForm .breadcrumbs .b-list", function () {
        if (!$(this).hasClass("loading")) {
          var customerId = $(this).attr("data-customerId"),
            customerName = $(this).text(),
            removeFromList = false;
          $("#pvpAgentPnlForm .breadcrumbs .b-list").each(function () {
            if (removeFromList) $(this).remove();
            if ($(this).attr("data-customerId") == customerId) {
              removeFromList = true;
            }
          });
          $("#pvpAgentPnlTable .divTableCell.sort").attr("data-sort", "").removeClass("asc").removeClass("desc");
          $("#pvpAgentPnlForm [name='pageNo']").val(1);
          $("#pvpAgentPnlForm .breadcrumbs .b-list").addClass("loading");
          $("#pvpAgentPnlList .tbl-link").addClass("loading");
          tcg.ac.getPvpCopyAgentPnl(customerId);
        }
      });
  };
  tcg.ac.ui.sortPvpCopyAgentPnl = function () {
    $(document)
      .off("click", "#pvpAgentPnlTable .divTableCell.sort")
      .on("click", "#pvpAgentPnlTable .divTableCell.sort", function (event) {
        var type = $(this).attr("data-type");
        var sort = $(this).attr("data-sort");
        $("#pvpAgentPnlTable .divTableCell.sort").addClass("loading");
        $("#pvpAgentPnlTable .divTableCell.sort").removeClass("asc").removeClass("desc");
        $("#pvpAgentPnlTable .divTableCell.sort").attr("data-sort", "");
        if (sort === "") {
          $(this).attr("data-sort", "desc");
          sort = "desc";
        } else {
          if (sort == "desc") {
            $(this).attr("data-sort", "asc");
            sort = "asc";
          } else {
            $(this).attr("data-sort", "desc");
            sort = "desc";
          }
        }
        $(this).addClass(sort);
        console.log(type, sort);
        $("#pvpAgentPnlForm input[name='pageNo']").val(1);
        var len = $("#pvpAgentPnlForm .b-list").length - 1;
        var customerId = $($("#pvpAgentPnlForm .b-list")[len]).attr("data-customerid");
        tcg.ac.getPvpCopyAgentPnl(customerId, type, sort);
      });
  };
  tcg.ac.ui.searchRNGCopyAgentPnl = function () {
    $(document)
      .off("click", "#rngAgentPnlForm .form-submit")
      .on("click", "#rngAgentPnlForm .form-submit", function (event) {
        $(".switchDecimal").removeClass("on").removeClass("off");
        if (event.originalEvent === undefined) {
          var len = $("#rngAgentPnlForm .b-list").length - 1;
          var customerId = $($("#rngAgentPnlForm .b-list")[len]).attr("data-customerid");
          if ($("#rngAgentPnlTable .divTableCell.sort").hasClass("asc")) {
            var type = $("#rngAgentPnlTable .divTableCell.sort.asc").attr("data-type");
            tcg.ac.getRNGCopyAgentPnl(customerId, type, "asc");
          } else if ($("#rngAgentPnlTable .divTableCell.sort").hasClass("desc")) {
            var type = $("#rngAgentPnlTable .divTableCell.sort.desc").attr("data-type");
            tcg.ac.getRNGCopyAgentPnl(customerId, type, "desc");
          } else {
            tcg.ac.getRNGCopyAgentPnl(customerId);
          }
        } else {
          $("#rngAgentPnlForm .breadcrumbs .b-list.new-entry").remove();
          tcg.ac.getRNGCopyAgentPnl();
        }
      });
  };
  tcg.ac.ui.loadRNGCopyAgentPnl = function (result, summaryType, sortType, sortDir) {
    var _column = "";
    var currentUser = {};
    for (var i = 0; i < result.list.length; i++) {
      if (tcg.ac.customerId === +result.list[i].customerId) {
        currentUser = result.list[i];
        break;
      }
    }
    if (summaryType == 0) {
      _column += "<div class='divTableCell'>" + tcg.lib.prop("ac_rng_pnl_summaryType_0_column_1") + "</div>";
      // _column += "<div class='divTableCell'>" +tcg.lib.prop("ac_rng_pnl_summaryType_0_column_2")+ "</div>";
      // _column += "<div class='divTableCell'>" +tcg.lib.prop("ac_rng_pnl_summaryType_0_column_3")+ "</div>";
      _column +=
        "<div class='divTableCell tbl-link sort' data-sort='' data-type='betAmount'>" +
        tcg.lib.prop("ac_rng_pnl_summaryType_0_column_4") +
        "</div>";
      _column +=
        "<div class='divTableCell tbl-link sort' data-sort='' data-type='winAmount'>" +
        tcg.lib.prop("ac_rng_pnl_summaryType_0_column_5") +
        "</div>";
      _column += "<div class='divTableCell'>" + tcg.lib.prop("ac_rng_pnl_summaryType_0_column_6") + "</div>";
      _column += "<div class='divTableCell'>" + tcg.lib.prop("ac_rng_pnl_summaryType_0_column_7") + "</div>";
      if (currentUser.merchantCharge != undefined && currentUser.merchantCharge != 0) {
        _column +=
          "<div class='divTableCell tbl-link' data-sort='' data-type='merchantCharge'>" +
          tcg.lib.prop("ac_lotto_pnl_summaryType_0_column_11") +
          "</div>";
      }
      _column +=
        "<div class='divTableCell tbl-link sort' data-sort='' data-type='profitAndLoss'>" +
        tcg.lib.prop("ac_rng_pnl_summaryType_0_column_8") +
        "</div>";
    } else {
      _column += "<div class='divTableCell'>" + tcg.lib.prop("ac_rng_pnl_summaryType_1_column_1") + "</div>";
      // _column += "<div class='divTableCell'>" +tcg.lib.prop("ac_rng_pnl_summaryType_1_column_2")+ "</div>";
      // _column += "<div class='divTableCell'>" +tcg.lib.prop("ac_rng_pnl_summaryType_1_column_3")+ "</div>";
      _column +=
        "<div class='divTableCell tbl-link sort' data-sort='' data-type='betAmount'>" +
        tcg.lib.prop("ac_rng_pnl_summaryType_1_column_4") +
        "</div>";
      _column +=
        "<div class='divTableCell tbl-link sort' data-sort='' data-type='winAmount'>" +
        tcg.lib.prop("ac_rng_pnl_summaryType_1_column_5") +
        "</div>";
      _column += "<div class='divTableCell'>" + tcg.lib.prop("ac_rng_pnl_summaryType_1_column_6") + "</div>";
      _column += "<div class='divTableCell'>" + tcg.lib.prop("ac_rng_pnl_summaryType_1_column_7") + "</div>";
      if (currentUser.merchantCharge != undefined && currentUser.merchantCharge != 0) {
        _column +=
          "<div class='divTableCell tbl-link' data-sort='' data-type='merchantCharge'>" +
          tcg.lib.prop("ac_lotto_pnl_summaryType_1_column_11") +
          "</div>";
      }
      _column +=
        "<div class='divTableCell tbl-link sort' data-sort='' data-type='profitAndLoss'>" +
        tcg.lib.prop("ac_rng_pnl_summaryType_1_column_8") +
        "</div>";
    }
    $("#rngAgentPnlColumn").html(_column);

    var decimal = 2,
      list = result.list,
      _html = "";
    if (list.length > 0 || list.length != null) {
      for (var i = 0; i < list.length; i++) {
        _html += "<div class='divTableRow'>";
        var account = list[i].customerName;
        _html +=
          "<div class='divTableCell tbl-link ps-strings' data-customerId='" + account + "'>" + account + "</div>";
        // var total_deposit = list[i].deposit;
        // _html += "<div class='divTableCell ps-num' data-switchDecimal='" + decimal + "' data-value='" + total_deposit + "'>" + tcg.ac.customCurrencyFormat(total_deposit, decimal) + "</div>";
        // var total_withdraw = list[i].withdraw;
        // _html += "<div class='divTableCell ps-num' data-switchDecimal='" + decimal + "' data-value='" + total_withdraw + "'>" + tcg.ac.customCurrencyFormat(total_withdraw, decimal) + "</div>";
        var total_betting_loss = list[i].betAmount;
        _html +=
          "<div class='divTableCell' data-switchDecimal='" +
          decimal +
          "' data-value='" +
          total_betting_loss +
          "'>" +
          tcg.ac.customCurrencyFormat(total_betting_loss, decimal) +
          "</div>";
        var total_game_winnings = list[i].winAmount;
        var gameWinningColor = total_game_winnings;
        _html +=
          "<div class='divTableCell " +
          gameWinningColor +
          "'  data-switchDecimal='" +
          decimal +
          "' data-value='" +
          total_game_winnings +
          "'>" +
          tcg.ac.customCurrencyFormat(total_game_winnings, decimal) +
          "</div>";
        var total_game_rebate = list[i].rebate + list[i].agentCommission;
        _html +=
          "<div class='divTableCell' data-switchDecimal='" +
          decimal +
          "' data-value='" +
          total_game_rebate +
          "'>" +
          tcg.ac.customCurrencyFormat(total_game_rebate, decimal) +
          "</div>";
        var total_promotion = list[i].promotion;
        _html +=
          "<div class='divTableCell' data-switchDecimal='" +
          decimal +
          "' data-value='" +
          total_promotion +
          "'>" +
          tcg.ac.customCurrencyFormat(total_promotion, decimal) +
          "</div>";
        if (currentUser.merchantCharge != undefined && currentUser.merchantCharge != 0) {
          _html +=
            "<div class='divTableCell' data-switchDecimal='" +
            decimal +
            "' data-value='" +
            list[i].merchantCharge +
            "'>" +
            tcg.ac.customCurrencyFormat(list[i].merchantCharge, decimal) +
            "</div>";
        }
        var total_pnl = list[i].profitAndLoss;
        var profitLossColor = total_pnl >= 0 ? "tbl-red" : "tbl-green";
        _html +=
          "<div class='divTableCell " +
          profitLossColor +
          "' data-switchDecimal='" +
          decimal +
          "' data-value='" +
          total_pnl +
          "'>" +
          tcg.ac.customCurrencyFormat(total_pnl, decimal) +
          "</div>";
        _html += "</div>";
      }
    } else {
      _html += "<div class='tableContent-wrp'>";
      _html += "<div class='noResult-data'>" + tcg.lib.prop("no_result") + "</div>";
      _html += "</div>";
    }
    $("#rngAgentPnlList").html(_html);
    if (sortType && sortDir) {
      $("#rngAgentPnlTable .divTableCell.tbl-link.sort[data-type='" + sortType + "']")
        .attr("data-sort", sortDir)
        .addClass(sortDir);
    }
  };
  tcg.ac.ui.switchRNGCopyAgentPnlSummaryType = function () {
    $(document)
      .off("click", "#rngAgentPnlForm .summaryTypeList li")
      .on("click", "#rngAgentPnlForm .summaryTypeList li", function () {
        if (!$(this).hasClass("active") && !$("#rngAgentPnlForm .form-submit").hasClass("processing")) {
          var rel = $(this).attr("data-rel");
          $("#rngAgentPnlForm .summaryTypeList li").removeClass("active");
          $(this).addClass("active");
          $("#rngAgentPnlTable .divTableCell.sort").attr("data-sort", "").removeClass("asc").removeClass("desc");
          $("#rngAgentPnlForm [name='pageNo']").val(1);
          $("#rngAgentPnlForm [name='summaryType']").val(rel);
          $("#rngAgentPnlForm [name='customerName']").val("");
          $("#rngAgentPnlForm .form-submit").click();
        }
      });
  };
  tcg.ac.ui.selectRNGCopyAgentDownlinePnl = function () {
    $(document)
      .off("click", "#rngAgentPnlList .tbl-link")
      .on("click", "#rngAgentPnlList .tbl-link", function () {
        if (!$(this).hasClass("loading")) {
          var customerName = $(this).text(),
            customerId = $(this).attr("data-customerId"),
            isOpen = false;
          $("#rngAgentPnlForm .breadcrumbs .b-list").each(function () {
            if ($(this).attr("data-customerId") == customerId) {
              isOpen = true;
            }
          });
          if (!isOpen) {
            $("#rngAgentPnlForm .breadcrumbs .b-list").addClass("loading");
            $("#rngAgentPnlForm .tbl-link").addClass("loading");
            var _html =
              "<div class='arrow-small-con mem-icon2 inline-block b-list new-entry' data-customerId='" +
              customerId +
              "'>" +
              customerName +
              "</div>";
            $("#rngAgentPnlForm .breadcrumbs").append(_html);
          }
          $("#rngAgentPnlTable .divTableCell.sort").attr("data-sort", "").removeClass("asc").removeClass("desc");
          $("#rngAgentPnlForm [name='pageNo']").val(1);
          //$("#rngAgentPnlForm [name='customerName']").val( customerName == window.sessionStorage.getItem("username") ? "" : customerName );
          tcg.ac.getRNGCopyAgentPnl(customerId);
        }
      });
  };
  tcg.ac.ui.clickRNGCopyAgentPnlBreadcrumbs = function () {
    $(document)
      .off("click", "#rngAgentPnlForm .breadcrumbs .b-list")
      .on("click", "#rngAgentPnlForm .breadcrumbs .b-list", function () {
        if (!$(this).hasClass("loading")) {
          var customerId = $(this).attr("data-customerId"),
            customerName = $(this).text(),
            removeFromList = false;
          $("#rngAgentPnlForm .breadcrumbs .b-list").each(function () {
            if (removeFromList) $(this).remove();
            if ($(this).attr("data-customerId") == customerId) {
              removeFromList = true;
            }
          });
          $("#rngAgentPnlTable .divTableCell.sort").attr("data-sort", "").removeClass("asc").removeClass("desc");
          $("#rngAgentPnlForm [name='pageNo']").val(1);
          $("#rngAgentPnlForm .breadcrumbs .b-list").addClass("loading");
          $("#rngAgentPnlList .tbl-link").addClass("loading");
          //$("#rngAgentPnlForm [name='customerName']").val( customerName == window.sessionStorage.getItem("username") ? "" : customerName );
          tcg.ac.getRNGCopyAgentPnl(customerId);
        }
      });
  };
  tcg.ac.ui.sortRNGCopyAgentPnl = function () {
    $(document)
      .off("click", "#rngAgentPnlTable .divTableCell.sort")
      .on("click", "#rngAgentPnlTable .divTableCell.sort", function (event) {
        var type = $(this).attr("data-type");
        var sort = $(this).attr("data-sort");
        $("#rngAgentPnlTable .divTableCell.sort").addClass("loading");
        $("#rngAgentPnlTable .divTableCell.sort").removeClass("asc").removeClass("desc");
        $("#rngAgentPnlTable .divTableCell.sort").attr("data-sort", "");
        if (sort === "") {
          $(this).attr("data-sort", "desc");
          sort = "desc";
        } else {
          if (sort == "desc") {
            $(this).attr("data-sort", "asc");
            sort = "asc";
          } else {
            $(this).attr("data-sort", "desc");
            sort = "desc";
          }
        }
        $(this).addClass(sort);
        console.log(type, sort);
        $("#rngAgentPnlForm input[name='pageNo']").val(1);
        var len = $("#rngAgentPnlForm .b-list").length - 1;
        var customerId = $($("#rngAgentPnlForm .b-list")[len]).attr("data-customerid");
        tcg.ac.getRNGCopyAgentPnl(customerId, type, sort);
      });
  };
  tcg.ac.ui.searchLIVECopyAgentPnl = function () {
    $(document)
      .off("click", "#liveAgentPnlForm .form-submit")
      .on("click", "#liveAgentPnlForm .form-submit", function (event) {
        $(".switchDecimal").removeClass("on").removeClass("off");
        if (event.originalEvent === undefined) {
          var len = $("#liveAgentPnlForm .b-list").length - 1;
          var customerId = $($("#liveAgentPnlForm .b-list")[len]).attr("data-customerid");
          if ($("#liveAgentPnlTable .divTableCell.sort").hasClass("asc")) {
            var type = $("#liveAgentPnlTable .divTableCell.sort.asc").attr("data-type");
            tcg.ac.getLIVECopyAgentPnl(customerId, type, "asc");
          } else if ($("#liveAgentPnlTable .divTableCell.sort").hasClass("desc")) {
            var type = $("#liveAgentPnlTable .divTableCell.sort.desc").attr("data-type");
            tcg.ac.getLIVECopyAgentPnl(customerId, type, "desc");
          } else {
            tcg.ac.getLIVECopyAgentPnl(customerId);
          }
        } else {
          $("#liveAgentPnlForm .breadcrumbs .b-list.new-entry").remove();
          tcg.ac.getLIVECopyAgentPnl();
        }
      });
  };
  tcg.ac.ui.loadLIVECopyAgentPnl = function (result, summaryType, sortType, sortDir) {
    var _column = "";
    var currentUser = {};
    for (var i = 0; i < result.list.length; i++) {
      if (tcg.ac.customerId === +result.list[i].customerId) {
        currentUser = result.list[i];
        break;
      }
    }
    if (summaryType == 0) {
      _column += "<div class='divTableCell'>" + tcg.lib.prop("ac_rng_pnl_summaryType_0_column_1") + "</div>";
      // _column += "<div class='divTableCell'>" +tcg.lib.prop("ac_rng_pnl_summaryType_0_column_2")+ "</div>";
      // _column += "<div class='divTableCell'>" +tcg.lib.prop("ac_rng_pnl_summaryType_0_column_3")+ "</div>";
      _column +=
        "<div class='divTableCell tbl-link sort' data-sort='' data-type='betAmount'>" +
        tcg.lib.prop("ac_rng_pnl_summaryType_0_column_4") +
        "</div>";
      _column += "<div class='divTableCell'>" + tcg.lib.prop("ac_rng_pnl_summaryType_0_column_9") + "</div>";
      _column +=
        "<div class='divTableCell tbl-link sort' data-sort='' data-type='winAmount'>" +
        tcg.lib.prop("ac_rng_pnl_summaryType_0_column_5") +
        "</div>";
      _column += "<div class='divTableCell'>" + tcg.lib.prop("ac_rng_pnl_summaryType_0_column_6") + "</div>";
      _column += "<div class='divTableCell'>" + tcg.lib.prop("ac_rng_pnl_summaryType_0_column_7") + "</div>";
      if (currentUser.merchantCharge != undefined && currentUser.merchantCharge != 0) {
        _column +=
          "<div class='divTableCell tbl-link' data-sort='' data-type='merchantCharge'>" +
          tcg.lib.prop("ac_lotto_pnl_summaryType_0_column_11") +
          "</div>";
      }
      _column +=
        "<div class='divTableCell tbl-link sort' data-sort='' data-type='profitAndLoss'>" +
        tcg.lib.prop("ac_rng_pnl_summaryType_0_column_8") +
        "</div>";
    } else {
      _column += "<div class='divTableCell'>" + tcg.lib.prop("ac_rng_pnl_summaryType_1_column_1") + "</div>";
      // _column += "<div class='divTableCell'>" +tcg.lib.prop("ac_rng_pnl_summaryType_1_column_2")+ "</div>";
      // _column += "<div class='divTableCell'>" +tcg.lib.prop("ac_rng_pnl_summaryType_1_column_3")+ "</div>";
      _column +=
        "<div class='divTableCell tbl-link sort' data-sort='' data-type='betAmount'>" +
        tcg.lib.prop("ac_rng_pnl_summaryType_1_column_4") +
        "</div>";
      _column += "<div class='divTableCell'>" + tcg.lib.prop("ac_rng_pnl_summaryType_1_column_9") + "</div>";
      _column +=
        "<div class='divTableCell tbl-link sort' data-sort='' data-type='winAmount'>" +
        tcg.lib.prop("ac_rng_pnl_summaryType_1_column_5") +
        "</div>";
      _column += "<div class='divTableCell'>" + tcg.lib.prop("ac_rng_pnl_summaryType_1_column_6") + "</div>";
      _column += "<div class='divTableCell'>" + tcg.lib.prop("ac_rng_pnl_summaryType_1_column_7") + "</div>";
      if (currentUser.merchantCharge != undefined && currentUser.merchantCharge != 0) {
        _column +=
          "<div class='divTableCell tbl-link' data-sort='' data-type='merchantCharge'>" +
          tcg.lib.prop("ac_lotto_pnl_summaryType_1_column_11") +
          "</div>";
      }
      _column +=
        "<div class='divTableCell tbl-link sort' data-sort='' data-type='profitAndLoss'>" +
        tcg.lib.prop("ac_rng_pnl_summaryType_1_column_8") +
        "</div>";
    }
    $("#liveAgentPnlColumn").html(_column);

    var decimal = 2,
      list = result.list,
      _html = "";
    if (list.length > 0 || list.length != null) {
      for (var i = 0; i < list.length; i++) {
        _html += "<div class='divTableRow'>";
        var account = list[i].customerName;
        _html +=
          "<div class='divTableCell tbl-link ps-strings' data-customerId='" + account + "'>" + account + "</div>";
        // var total_deposit = list[i].deposit;
        // _html += "<div class='divTableCell ps-num' data-switchDecimal='" + decimal + "' data-value='" + total_deposit + "'>" + tcg.ac.customCurrencyFormat(total_deposit, decimal) + "</div>";
        // var total_withdraw = list[i].withdraw;
        // _html += "<div class='divTableCell ps-num' data-switchDecimal='" + decimal + "' data-value='" + total_withdraw + "'>" + tcg.ac.customCurrencyFormat(total_withdraw, decimal) + "</div>";
        var total_betting_loss = list[i].betAmount;
        _html +=
          "<div class='divTableCell' data-switchDecimal='" +
          decimal +
          "' data-value='" +
          total_betting_loss +
          "'>" +
          tcg.ac.customCurrencyFormat(total_betting_loss, decimal) +
          "</div>";
        var total_game_winnings = list[i].winAmount;
        var gameWinningColor = total_game_winnings;
        var validBetAmount = list[i].validBetAmount;
        _html +=
          "<div class='divTableCell  data-switchDecimal='" +
          decimal +
          "' data-value='" +
          validBetAmount +
          "'>" +
          tcg.ac.customCurrencyFormat(validBetAmount, decimal) +
          "</div>";
        _html +=
          "<div class='divTableCell " +
          gameWinningColor +
          "'  data-switchDecimal='" +
          decimal +
          "' data-value='" +
          total_game_winnings +
          "'>" +
          tcg.ac.customCurrencyFormat(total_game_winnings, decimal) +
          "</div>";
        var total_game_rebate = list[i].rebate + list[i].agentCommission;
        _html +=
          "<div class='divTableCell' data-switchDecimal='" +
          decimal +
          "' data-value='" +
          total_game_rebate +
          "'>" +
          tcg.ac.customCurrencyFormat(total_game_rebate, decimal) +
          "</div>";
        var total_promotion = list[i].promotion;
        _html +=
          "<div class='divTableCell' data-switchDecimal='" +
          decimal +
          "' data-value='" +
          total_promotion +
          "'>" +
          tcg.ac.customCurrencyFormat(total_promotion, decimal) +
          "</div>";
        if (currentUser.merchantCharge != undefined && currentUser.merchantCharge != 0) {
          _html +=
            "<div class='divTableCell' data-switchDecimal='" +
            decimal +
            "' data-value='" +
            list[i].merchantCharge +
            "'>" +
            tcg.ac.customCurrencyFormat(list[i].merchantCharge, decimal) +
            "</div>";
        }
        var total_pnl = list[i].profitAndLoss;
        var profitLossColor = total_pnl >= 0 ? "tbl-red" : "tbl-green";
        _html +=
          "<div class='divTableCell " +
          profitLossColor +
          "' data-switchDecimal='" +
          decimal +
          "' data-value='" +
          total_pnl +
          "'>" +
          tcg.ac.customCurrencyFormat(total_pnl, decimal) +
          "</div>";
        _html += "</div>";
      }
    } else {
      _html += "<div class='tableContent-wrp'>";
      _html += "<div class='noResult-data'>" + tcg.lib.prop("no_result") + "</div>";
      _html += "</div>";
    }
    $("#liveAgentPnlList").html(_html);
    if (sortType && sortDir) {
      $("#liveAgentPnlTable .divTableCell.tbl-link.sort[data-type='" + sortType + "']")
        .attr("data-sort", sortDir)
        .addClass(sortDir);
    }
  };
  tcg.ac.ui.switchLIVECopyAgentPnlSummaryType = function () {
    $(document)
      .off("click", "#liveAgentPnlForm .summaryTypeList li")
      .on("click", "#liveAgentPnlForm .summaryTypeList li", function () {
        if (!$(this).hasClass("active") && !$("#liveAgentPnlForm .form-submit").hasClass("processing")) {
          var rel = $(this).attr("data-rel");
          $("#liveAgentPnlForm .summaryTypeList li").removeClass("active");
          $(this).addClass("active");
          $("#liveAgentPnlTable .divTableCell.sort").attr("data-sort", "").removeClass("asc").removeClass("desc");
          $("#liveAgentPnlForm [name='pageNo']").val(1);
          $("#liveAgentPnlForm [name='summaryType']").val(rel);
          $("#liveAgentPnlForm [name='customerName']").val("");
          $("#liveAgentPnlForm .form-submit").click();
        }
      });
  };
  tcg.ac.ui.selectLIVECopyAgentDownlinePnl = function () {
    $(document)
      .off("click", "#liveAgentPnlList .tbl-link")
      .on("click", "#liveAgentPnlList .tbl-link", function () {
        if (!$(this).hasClass("loading")) {
          var customerName = $(this).text(),
            customerId = $(this).attr("data-customerId"),
            isOpen = false;
          $("#liveAgentPnlForm .breadcrumbs .b-list").each(function () {
            if ($(this).attr("data-customerId") == customerId) {
              isOpen = true;
            }
          });
          if (!isOpen) {
            $("#liveAgentPnlForm .breadcrumbs .b-list").addClass("loading");
            $("#liveAgentPnlForm .tbl-link").addClass("loading");
            var _html =
              "<div class='arrow-small-con mem-icon2 inline-block b-list new-entry' data-customerId='" +
              customerId +
              "'>" +
              customerName +
              "</div>";
            $("#liveAgentPnlForm .breadcrumbs").append(_html);
          }
          $("#liveAgentPnlTable .divTableCell.sort").attr("data-sort", "").removeClass("asc").removeClass("desc");
          $("#liveAgentPnlForm [name='pageNo']").val(1);
          //$("#rngAgentPnlForm [name='customerName']").val( customerName == window.sessionStorage.getItem("username") ? "" : customerName );
          tcg.ac.getLIVECopyAgentPnl(customerId);
        }
      });
  };
  tcg.ac.ui.clickLIVECopyAgentPnlBreadcrumbs = function () {
    $(document)
      .off("click", "#liveAgentPnlForm .breadcrumbs .b-list")
      .on("click", "#liveAgentPnlForm .breadcrumbs .b-list", function () {
        if (!$(this).hasClass("loading")) {
          var customerId = $(this).attr("data-customerId"),
            customerName = $(this).text(),
            removeFromList = false;
          $("#liveAgentPnlForm .breadcrumbs .b-list").each(function () {
            if (removeFromList) $(this).remove();
            if ($(this).attr("data-customerId") == customerId) {
              removeFromList = true;
            }
          });
          $("#liveAgentPnlTable .divTableCell.sort").attr("data-sort", "").removeClass("asc").removeClass("desc");
          $("#liveAgentPnlForm [name='pageNo']").val(1);
          $("#liveAgentPnlForm .breadcrumbs .b-list").addClass("loading");
          $("#liveAgentPnlList .tbl-link").addClass("loading");
          //$("#rngAgentPnlForm [name='customerName']").val( customerName == window.sessionStorage.getItem("username") ? "" : customerName );
          tcg.ac.getLIVECopyAgentPnl(customerId);
        }
      });
  };
  tcg.ac.ui.sortLIVECopyAgentPnl = function () {
    $(document)
      .off("click", "#liveAgentPnlTable .divTableCell.sort")
      .on("click", "#liveAgentPnlTable .divTableCell.sort", function (event) {
        var type = $(this).attr("data-type");
        var sort = $(this).attr("data-sort");
        $("#liveAgentPnlTable .divTableCell.sort").addClass("loading");
        $("#liveAgentPnlTable .divTableCell.sort").removeClass("asc").removeClass("desc");
        $("#liveAgentPnlTable .divTableCell.sort").attr("data-sort", "");
        if (sort === "") {
          $(this).attr("data-sort", "desc");
          sort = "desc";
        } else {
          if (sort == "desc") {
            $(this).attr("data-sort", "asc");
            sort = "asc";
          } else {
            $(this).attr("data-sort", "desc");
            sort = "desc";
          }
        }
        $(this).addClass(sort);
        console.log(type, sort);
        $("#liveAgentPnlForm input[name='pageNo']").val(1);
        var len = $("#liveAgentPnlForm .b-list").length - 1;
        var customerId = $($("#liveAgentPnlForm .b-list")[len]).attr("data-customerid");
        tcg.ac.getLIVECopyAgentPnl(customerId, type, sort);
      });
  };
  tcg.ac.ui.searchFishCopyAgentPnl = function () {
    $(document)
      .off("click", "#fishAgentPnlForm .form-submit")
      .on("click", "#fishAgentPnlForm .form-submit", function (event) {
        $(".switchDecimal").removeClass("on").removeClass("off");
        if (event.originalEvent === undefined) {
          var len = $("#fishAgentPnlForm .b-list").length - 1;
          var customerId = $($("#fishAgentPnlForm .b-list")[len]).attr("data-customerid");
          if ($("#fishAgentPnlTable .divTableCell.sort").hasClass("asc")) {
            var type = $("#fishAgentPnlTable .divTableCell.sort.asc").attr("data-type");
            tcg.ac.getFishCopyAgentPnl(customerId, type, "asc");
          } else if ($("#fishAgentPnlTable .divTableCell.sort").hasClass("desc")) {
            var type = $("#fishAgentPnlTable .divTableCell.sort.desc").attr("data-type");
            tcg.ac.getFishCopyAgentPnl(customerId, type, "desc");
          } else {
            tcg.ac.getFishCopyAgentPnl(customerId);
          }
        } else {
          $("#fishAgentPnlForm .breadcrumbs .b-list.new-entry").remove();
          tcg.ac.getFishCopyAgentPnl();
        }
      });
  };
  tcg.ac.ui.loadFishCopyAgentPnl = function (result, summaryType, sortType, sortDir) {
    var _column = "";
    var currentUser = {};
    for (var i = 0; i < result.list.length; i++) {
      if (tcg.ac.customerId === +result.list[i].customerId) {
        currentUser = result.list[i];
        break;
      }
    }
    if (summaryType == 0) {
      _column += "<div class='divTableCell'>" + tcg.lib.prop("ac_fish_pnl_summaryType_0_column_1") + "</div>";
      // _column += "<div class='divTableCell'>" +tcg.lib.prop("ac_fish_pnl_summaryType_0_column_2")+ "</div>";
      // _column += "<div class='divTableCell'>" +tcg.lib.prop("ac_fish_pnl_summaryType_0_column_3")+ "</div>";
      _column +=
        "<div class='divTableCell tbl-link sort' data-sort='' data-type='betAmount'>" +
        tcg.lib.prop("ac_fish_pnl_summaryType_0_column_4") +
        "</div>";
      _column +=
        "<div class='divTableCell tbl-link sort' data-sort='' data-type='winAmount'>" +
        tcg.lib.prop("ac_fish_pnl_summaryType_0_column_5") +
        "</div>";
      _column += "<div class='divTableCell'>" + tcg.lib.prop("ac_fish_pnl_summaryType_0_column_6") + "</div>";
      _column += "<div class='divTableCell'>" + tcg.lib.prop("ac_fish_pnl_summaryType_0_column_7") + "</div>";
      if (currentUser.merchantCharge != undefined && currentUser.merchantCharge != 0) {
        _column +=
          "<div class='divTableCell tbl-link' data-sort='' data-type='merchantCharge'>" +
          tcg.lib.prop("ac_lotto_pnl_summaryType_0_column_11") +
          "</div>";
      }
      _column +=
        "<div class='divTableCell tbl-link sort' data-sort='' data-type='profitAndLoss'>" +
        tcg.lib.prop("ac_fish_pnl_summaryType_0_column_8") +
        "</div>";
    } else {
      _column += "<div class='divTableCell'>" + tcg.lib.prop("ac_fish_pnl_summaryType_1_column_1") + "</div>";
      // _column += "<div class='divTableCell'>" +tcg.lib.prop("ac_fish_pnl_summaryType_1_column_2")+ "</div>";
      // _column += "<div class='divTableCell'>" +tcg.lib.prop("ac_fish_pnl_summaryType_1_column_3")+ "</div>";
      _column +=
        "<div class='divTableCell tbl-link sort' data-sort='' data-type='betAmount'>" +
        tcg.lib.prop("ac_fish_pnl_summaryType_1_column_4") +
        "</div>";
      _column +=
        "<div class='divTableCell tbl-link sort' data-sort='' data-type='winAmount'>" +
        tcg.lib.prop("ac_fish_pnl_summaryType_1_column_5") +
        "</div>";
      _column += "<div class='divTableCell'>" + tcg.lib.prop("ac_fish_pnl_summaryType_1_column_6") + "</div>";
      if (currentUser.merchantCharge != undefined && currentUser.merchantCharge != 0) {
        _column +=
          "<div class='divTableCell tbl-link' data-sort='' data-type='merchantCharge'>" +
          tcg.lib.prop("ac_lotto_pnl_summaryType_1_column_11") +
          "</div>";
      }
      _column += "<div class='divTableCell'>" + tcg.lib.prop("ac_fish_pnl_summaryType_1_column_7") + "</div>";
      _column +=
        "<div class='divTableCell tbl-link sort' data-sort='' data-type='profitAndLoss'>" +
        tcg.lib.prop("ac_fish_pnl_summaryType_1_column_8") +
        "</div>";
    }
    $("#fishAgentPnlColumn").html(_column);

    var decimal = 2,
      list = result.list,
      _html = "";
    if (list.length > 0 || list.length != null) {
      for (var i = 0; i < list.length; i++) {
        _html += "<div class='divTableRow'>";
        var account = list[i].customerName;
        _html +=
          "<div class='divTableCell tbl-link ps-strings' data-customerId='" + account + "'>" + account + "</div>";
        // var total_deposit = list[i].deposit;
        // _html += "<div class='divTableCell ps-num' data-switchDecimal='" + decimal + "' data-value='" + total_deposit + "'>" + tcg.ac.customCurrencyFormat(total_deposit, decimal) + "</div>";
        // var total_withdraw = list[i].withdraw;
        // _html += "<div class='divTableCell ps-num' data-switchDecimal='" + decimal + "' data-value='" + total_withdraw + "'>" + tcg.ac.customCurrencyFormat(total_withdraw, decimal) + "</div>";
        var total_betting_loss = list[i].betAmount;
        _html +=
          "<div class='divTableCell' data-switchDecimal='" +
          decimal +
          "' data-value='" +
          total_betting_loss +
          "'>" +
          tcg.ac.customCurrencyFormat(total_betting_loss, decimal) +
          "</div>";
        var total_game_winnings = list[i].winAmount;
        var gameWinningColor = total_game_winnings;
        _html +=
          "<div class='divTableCell " +
          gameWinningColor +
          "'  data-switchDecimal='" +
          decimal +
          "' data-value='" +
          total_game_winnings +
          "'>" +
          tcg.ac.customCurrencyFormat(total_game_winnings, decimal) +
          "</div>";
        var total_game_rebate = list[i].rebate + list[i].agentCommission;
        _html +=
          "<div class='divTableCell' data-switchDecimal='" +
          decimal +
          "' data-value='" +
          total_game_rebate +
          "'>" +
          tcg.ac.customCurrencyFormat(total_game_rebate, decimal) +
          "</div>";
        var total_promotion = list[i].promotion;
        _html +=
          "<div class='divTableCell' data-switchDecimal='" +
          decimal +
          "' data-value='" +
          total_promotion +
          "'>" +
          tcg.ac.customCurrencyFormat(total_promotion, decimal) +
          "</div>";
        if (currentUser.merchantCharge != undefined && currentUser.merchantCharge != 0) {
          _html +=
            "<div class='divTableCell' data-switchDecimal='" +
            decimal +
            "' data-value='" +
            list[i].merchantCharge +
            "'>" +
            tcg.ac.customCurrencyFormat(list[i].merchantCharge, decimal) +
            "</div>";
        }
        var total_pnl = list[i].profitAndLoss;
        var profitLossColor = total_pnl >= 0 ? "tbl-red" : "tbl-green";
        _html +=
          "<div class='divTableCell " +
          profitLossColor +
          "' data-switchDecimal='" +
          decimal +
          "' data-value='" +
          total_pnl +
          "'>" +
          tcg.ac.customCurrencyFormat(total_pnl, decimal) +
          "</div>";
        _html += "</div>";
      }
    } else {
      _html += "<div class='tableContent-wrp'>";
      _html += "<div class='noResult-data'>" + tcg.lib.prop("no_result") + "</div>";
      _html += "</div>";
    }
    $("#fishAgentPnlList").html(_html);
    if (sortType && sortDir) {
      $("#fishAgentPnlTable .divTableCell.tbl-link.sort[data-type='" + sortType + "']")
        .attr("data-sort", sortDir)
        .addClass(sortDir);
    }
  };
  tcg.ac.ui.switchFishCopyAgentPnlSummaryType = function () {
    $(document)
      .off("click", "#fishAgentPnlForm .summaryTypeList li")
      .on("click", "#fishAgentPnlForm .summaryTypeList li", function () {
        if (!$(this).hasClass("active") && !$("#rngAgentPnlForm .form-submit").hasClass("processing")) {
          var rel = $(this).attr("data-rel");
          $("#fishAgentPnlForm .summaryTypeList li").removeClass("active");
          $(this).addClass("active");
          $("#fishAgentPnlTable .divTableCell.sort").attr("data-sort", "").removeClass("asc").removeClass("desc");
          $("#fishAgentPnlForm [name='pageNo']").val(1);
          $("#fishAgentPnlForm [name='summaryType']").val(rel);
          $("#fishAgentPnlForm [name='customerName']").val("");
          $("#fishAgentPnlForm .form-submit").click();
        }
      });
  };
  tcg.ac.ui.selectFishCopyAgentDownlinePnl = function () {
    $(document)
      .off("click", "#fishAgentPnlList .tbl-link")
      .on("click", "#fishAgentPnlList .tbl-link", function () {
        if (!$(this).hasClass("loading")) {
          var customerName = $(this).text(),
            customerId = $(this).attr("data-customerId"),
            isOpen = false;
          $("#fishAgentPnlForm .breadcrumbs .b-list").each(function () {
            if ($(this).attr("data-customerId") == customerId) {
              isOpen = true;
            }
          });
          if (!isOpen) {
            $("#fishAgentPnlForm .breadcrumbs .b-list").addClass("loading");
            $("#fishAgentPnlForm .tbl-link").addClass("loading");
            var _html =
              "<div class='arrow-small-con mem-icon2 inline-block b-list new-entry' data-customerId='" +
              customerId +
              "'>" +
              customerName +
              "</div>";
            $("#fishAgentPnlForm .breadcrumbs").append(_html);
          }
          $("#fishAgentPnlTable .divTableCell.sort").attr("data-sort", "").removeClass("asc").removeClass("desc");
          $("#fishAgentPnlForm [name='pageNo']").val(1);
          // $("#fishAgentPnlForm [name='customerName']").val( customerName == window.sessionStorage.getItem("username") ? "" : customerName );
          tcg.ac.getFishCopyAgentPnl(customerId);
        }
      });
  };
  tcg.ac.ui.clickFishCopyAgentPnlBreadcrumbs = function () {
    $(document)
      .off("click", "#fishAgentPnlForm .breadcrumbs .b-list")
      .on("click", "#fishAgentPnlForm .breadcrumbs .b-list", function () {
        if (!$(this).hasClass("loading")) {
          var customerId = $(this).attr("data-customerId"),
            customerName = $(this).text(),
            removeFromList = false;
          $("#fishAgentPnlForm .breadcrumbs .b-list").each(function () {
            if (removeFromList) $(this).remove();
            if ($(this).attr("data-customerId") == customerId) {
              removeFromList = true;
            }
          });
          $("#fishAgentPnlTable .divTableCell.sort").attr("data-sort", "").removeClass("asc").removeClass("desc");
          $("#fishAgentPnlForm [name='pageNo']").val(1);
          $("#fishAgentPnlForm .breadcrumbs .b-list").addClass("loading");
          $("#fishAgentPnlForm .tbl-link").addClass("loading");
          $("#fishAgentPnlForm [name='customerName']").val(
            customerName == window.sessionStorage.getItem("username") ? "" : customerName
          );
          tcg.ac.getFishCopyAgentPnl(customerId);
        }
      });
  };
  tcg.ac.ui.sortFishCopyAgentPnl = function () {
    $(document)
      .off("click", "#fishAgentPnlTable .divTableCell.sort")
      .on("click", "#fishAgentPnlTable .divTableCell.sort", function (event) {
        var type = $(this).attr("data-type");
        var sort = $(this).attr("data-sort");
        $("#fishAgentPnlTable .divTableCell.sort").addClass("loading");
        $("#fishAgentPnlTable .divTableCell.sort").removeClass("asc").removeClass("desc");
        $("#fishAgentPnlTable .divTableCell.sort").attr("data-sort", "");
        if (sort === "") {
          $(this).attr("data-sort", "desc");
          sort = "desc";
        } else {
          if (sort == "desc") {
            $(this).attr("data-sort", "asc");
            sort = "asc";
          } else {
            $(this).attr("data-sort", "desc");
            sort = "desc";
          }
        }
        $(this).addClass(sort);
        console.log(type, sort);
        $("#fishAgentPnlForm input[name='pageNo']").val(1);
        var len = $("#fishAgentPnlForm .b-list").length - 1;
        var customerId = $($("#fishAgentPnlForm .b-list")[len]).attr("data-customerid");
        tcg.ac.getFishCopyAgentPnl(customerId, type, sort);
      });
  };
  tcg.ac.ui.searchSportsCopyAgentPnl = function () {
    $(document)
      .off("click", "#sportsAgentPnlForm .form-submit")
      .on("click", "#sportsAgentPnlForm .form-submit", function (event) {
        $(".switchDecimal").removeClass("on").removeClass("off");
        if (event.originalEvent === undefined) {
          var len = $("#sportsAgentPnlForm .b-list").length - 1;
          var customerId = $($("#sportsAgentPnlForm .b-list")[len]).attr("data-customerid");
          if ($("#sportsAgentPnlTable .divTableCell.sort").hasClass("asc")) {
            var type = $("#sportsAgentPnlTable .divTableCell.sort.asc").attr("data-type");
            tcg.ac.getSportsCopyAgentPnl(customerId, type, "asc");
          } else if ($("#sportsAgentPnlTable .divTableCell.sort").hasClass("desc")) {
            var type = $("#sportsAgentPnlTable .divTableCell.sort.desc").attr("data-type");
            tcg.ac.getSportsCopyAgentPnl(customerId, type, "desc");
          } else {
            tcg.ac.getSportsCopyAgentPnl(customerId);
          }
        } else {
          $("#sportsAgentPnlForm .breadcrumbs .b-list.new-entry").remove();
          tcg.ac.getSportsCopyAgentPnl();
        }
      });
  };
  tcg.ac.ui.loadSportsCopyAgentPnl = function (result, summaryType, sortType, sortDir) {
    var _column = "";
    var currentUser = {};
    for (var i = 0; i < result.list.length; i++) {
      if (tcg.ac.customerId === +result.list[i].customerId) {
        currentUser = result.list[i];
        break;
      }
    }
    if (summaryType == 0) {
      _column += "<div class='divTableCell'>" + tcg.lib.prop("ac_sports_pnl_summaryType_0_column_1") + "</div>";
      // _column += "<div class='divTableCell'>" +tcg.lib.prop("ac_sports_pnl_summaryType_0_column_2")+ "</div>";
      // _column += "<div class='divTableCell'>" +tcg.lib.prop("ac_sports_pnl_summaryType_0_column_3")+ "</div>";
      _column +=
        "<div class='divTableCell tbl-link sort' data-sort='' data-type='betAmount'>" +
        tcg.lib.prop("ac_sports_pnl_summaryType_0_column_4") +
        "</div>";
      _column += "<div class='divTableCell'>" + tcg.lib.prop("ac_sports_pnl_summaryType_0_column_9") + "</div>";
      _column +=
        "<div class='divTableCell tbl-link sort' data-sort='' data-type='winAmount'>" +
        tcg.lib.prop("ac_sports_pnl_summaryType_0_column_5") +
        "</div>";
      _column += "<div class='divTableCell'>" + tcg.lib.prop("ac_sports_pnl_summaryType_0_column_6") + "</div>";
      _column += "<div class='divTableCell'>" + tcg.lib.prop("ac_sports_pnl_summaryType_0_column_7") + "</div>";
      if (currentUser.merchantCharge != undefined && currentUser.merchantCharge != 0) {
        _column +=
          "<div class='divTableCell tbl-link' data-sort='' data-type='merchantCharge'>" +
          tcg.lib.prop("ac_lotto_pnl_summaryType_0_column_11") +
          "</div>";
      }
      _column +=
        "<div class='divTableCell tbl-link sort' data-sort='' data-type='profitAndLoss'>" +
        tcg.lib.prop("ac_sports_pnl_summaryType_0_column_8") +
        "</div>";
    } else {
      _column += "<div class='divTableCell'>" + tcg.lib.prop("ac_sports_pnl_summaryType_1_column_1") + "</div>";
      // _column += "<div class='divTableCell'>" +tcg.lib.prop("ac_sports_pnl_summaryType_1_column_2")+ "</div>";
      // _column += "<div class='divTableCell'>" +tcg.lib.prop("ac_sports_pnl_summaryType_1_column_3")+ "</div>";
      _column +=
        "<div class='divTableCell tbl-link sort' data-sort='' data-type='betAmount'>" +
        tcg.lib.prop("ac_sports_pnl_summaryType_1_column_4") +
        "</div>";
      _column += "<div class='divTableCell'>" + tcg.lib.prop("ac_sports_pnl_summaryType_1_column_9") + "</div>";
      _column +=
        "<div class='divTableCell tbl-link sort' data-sort='' data-type='winAmount'>" +
        tcg.lib.prop("ac_sports_pnl_summaryType_1_column_5") +
        "</div>";
      _column += "<div class='divTableCell'>" + tcg.lib.prop("ac_sports_pnl_summaryType_1_column_6") + "</div>";
      _column += "<div class='divTableCell'>" + tcg.lib.prop("ac_sports_pnl_summaryType_1_column_7") + "</div>";
      if (currentUser.merchantCharge != undefined && currentUser.merchantCharge != 0) {
        _column +=
          "<div class='divTableCell tbl-link' data-sort='' data-type='merchantCharge'>" +
          tcg.lib.prop("ac_lotto_pnl_summaryType_1_column_11") +
          "</div>";
      }
      _column +=
        "<div class='divTableCell tbl-link sort' data-sort='' data-type='profitAndLoss'>" +
        tcg.lib.prop("ac_sports_pnl_summaryType_1_column_8") +
        "</div>";
    }
    $("#sportsAgentPnlColumn").html(_column);

    var decimal = 2,
      list = result.list,
      _html = "";
    if (list.length > 0 || list.length != null) {
      for (var i = 0; i < list.length; i++) {
        _html += "<div class='divTableRow'>";
        var account = list[i].customerName;
        _html +=
          "<div class='divTableCell tbl-link ps-strings' data-customerId='" + account + "'>" + account + "</div>";
        // var total_deposit = list[i].deposit;
        // _html += "<div class='divTableCell' data-switchDecimal='" + decimal + "' data-value='" + total_deposit + "'>" + tcg.ac.customCurrencyFormat(total_deposit, decimal) + "</div>";
        // var total_withdraw = list[i].withdraw;
        // _html += "<div class='divTableCell' data-switchDecimal='" + decimal + "' data-value='" + total_withdraw + "'>" + tcg.ac.customCurrencyFormat(total_withdraw, decimal) + "</div>";
        var total_betting_loss = list[i].betAmount;
        _html +=
          "<div class='divTableCell' data-switchDecimal='" +
          decimal +
          "' data-value='" +
          total_betting_loss +
          "'>" +
          tcg.ac.customCurrencyFormat(total_betting_loss, decimal) +
          "</div>";
        var total_game_winnings = list[i].winAmount;
        var gameWinningColor = total_game_winnings;
        var validBetAmount = list[i].validBetAmount;
        _html +=
          "<div class='divTableCell  data-switchDecimal='" +
          decimal +
          "' data-value='" +
          validBetAmount +
          "'>" +
          tcg.ac.customCurrencyFormat(validBetAmount, decimal) +
          "</div>";
        _html +=
          "<div class='divTableCell " +
          gameWinningColor +
          "'  data-switchDecimal='" +
          decimal +
          "' data-value='" +
          total_game_winnings +
          "'>" +
          tcg.ac.customCurrencyFormat(total_game_winnings, decimal) +
          "</div>";
        var total_game_rebate = list[i].agentCommission + list[i].rebate;
        _html +=
          "<div class='divTableCell' data-switchDecimal='" +
          decimal +
          "' data-value='" +
          total_game_rebate +
          "'>" +
          tcg.ac.customCurrencyFormat(total_game_rebate, decimal) +
          "</div>";
        var total_promotion = list[i].promotion;
        _html +=
          "<div class='divTableCell' data-switchDecimal='" +
          decimal +
          "' data-value='" +
          total_promotion +
          "'>" +
          tcg.ac.customCurrencyFormat(total_promotion, decimal) +
          "</div>";
        if (currentUser.merchantCharge != undefined && currentUser.merchantCharge != 0) {
          _html +=
            "<div class='divTableCell' data-switchDecimal='" +
            decimal +
            "' data-value='" +
            list[i].merchantCharge +
            "'>" +
            tcg.ac.customCurrencyFormat(list[i].merchantCharge, decimal) +
            "</div>";
        }
        var total_pnl = list[i].profitAndLoss;
        var profitLossColor = total_pnl >= 0 ? "tbl-red" : "tbl-green";
        _html +=
          "<div class='divTableCell " +
          profitLossColor +
          "' data-switchDecimal='" +
          decimal +
          "' data-value='" +
          total_pnl +
          "'>" +
          tcg.ac.customCurrencyFormat(total_pnl, decimal) +
          "</div>";
        _html += "</div>";
      }
    } else {
      _html += "<div class='tableContent-wrp'>";
      _html += "<div class='noResult-data'>" + tcg.lib.prop("no_result") + "</div>";
      _html += "</div>";
    }
    $("#sportsAgentPnlList").html(_html);
    if (sortType && sortDir) {
      $("#sportsAgentPnlTable .divTableCell.tbl-link.sort[data-type='" + sortType + "']")
        .attr("data-sort", sortDir)
        .addClass(sortDir);
    }
  };
  tcg.ac.ui.switchSportsCopyAgentPnlSummaryType = function () {
    $(document)
      .off("click", "#sportsAgentPnlForm .summaryTypeList li")
      .on("click", "#sportsAgentPnlForm .summaryTypeList li", function () {
        if (!$(this).hasClass("active") && !$("#rngAgentPnlForm .form-submit").hasClass("processing")) {
          var rel = $(this).attr("data-rel");
          $("#sportsAgentPnlForm .summaryTypeList li").removeClass("active");
          $(this).addClass("active");
          $("#sportsAgentPnlTable .divTableCell.sort").attr("data-sort", "").removeClass("asc").removeClass("desc");
          $("#sportsAgentPnlForm [name='pageNo']").val(1);
          $("#sportsAgentPnlForm [name='summaryType']").val(rel);
          $("#sportsAgentPnlForm [name='customerName']").val("");
          $("#sportsAgentPnlForm .form-submit").click();
        }
      });
  };
  tcg.ac.ui.selectSportsCopyAgentDownlinePnl = function () {
    $(document)
      .off("click", "#sportsAgentPnlList .tbl-link")
      .on("click", "#sportsAgentPnlList .tbl-link", function () {
        if (!$(this).hasClass("loading")) {
          var customerName = $(this).text(),
            customerId = $(this).attr("data-customerId"),
            isOpen = false;
          $("#sportsAgentPnlForm .breadcrumbs .b-list").each(function () {
            if ($(this).attr("data-customerId") == customerId) {
              isOpen = true;
            }
          });
          if (!isOpen) {
            $("#sportsAgentPnlForm .breadcrumbs .b-list").addClass("loading");
            $("#sportsAgentPnlForm .tbl-link").addClass("loading");
            var _html =
              "<div class='arrow-small-con mem-icon2 inline-block b-list new-entry' data-customerId='" +
              customerId +
              "'>" +
              customerName +
              "</div>";
            $("#sportsAgentPnlForm .breadcrumbs").append(_html);
          }
          $("#sportsAgentPnlTable .divTableCell.sort").attr("data-sort", "").removeClass("asc").removeClass("desc");
          $("#sportsAgentPnlForm [name='pageNo']").val(1);
          // $("#sportsAgentPnlForm [name='customerName']").val( customerName == window.sessionStorage.getItem("username") ? "" : customerName );
          tcg.ac.getSportsCopyAgentPnl(customerId);
        }
      });
  };
  tcg.ac.ui.clickSportsCopyAgentPnlBreadcrumbs = function () {
    $(document)
      .off("click", "#sportsAgentPnlForm .breadcrumbs .b-list")
      .on("click", "#sportsAgentPnlForm .breadcrumbs .b-list", function () {
        if (!$(this).hasClass("loading")) {
          var customerId = $(this).attr("data-customerId"),
            customerName = $(this).text(),
            removeFromList = false;
          $("#sportsAgentPnlForm .breadcrumbs .b-list").each(function () {
            if (removeFromList) $(this).remove();
            if ($(this).attr("data-customerId") == customerId) {
              removeFromList = true;
            }
          });
          $("#sportsAgentPnlTable .divTableCell.sort").attr("data-sort", "").removeClass("asc").removeClass("desc");
          $("#sportsAgentPnlForm [name='pageNo']").val(1);
          $("#sportsAgentPnlForm .breadcrumbs .b-list").addClass("loading");
          $("#sportsAgentPnlForm .tbl-link").addClass("loading");
          $("#sportsAgentPnlForm [name='customerName']").val(
            customerName == window.sessionStorage.getItem("username") ? "" : customerName
          );
          tcg.ac.getSportsCopyAgentPnl(customerId);
        }
      });
  };
  tcg.ac.ui.sortSportsCopyAgentPnl = function () {
    $(document)
      .off("click", "#sportsAgentPnlTable .divTableCell.sort")
      .on("click", "#sportsAgentPnlTable .divTableCell.sort", function (event) {
        var type = $(this).attr("data-type");
        var sort = $(this).attr("data-sort");
        $("#sportsAgentPnlTable .divTableCell.sort").addClass("loading");
        $("#sportsAgentPnlTable .divTableCell.sort").removeClass("asc").removeClass("desc");
        $("#sportsAgentPnlTable .divTableCell.sort").attr("data-sort", "");
        if (sort === "") {
          $(this).attr("data-sort", "desc");
          sort = "desc";
        } else {
          if (sort == "desc") {
            $(this).attr("data-sort", "asc");
            sort = "asc";
          } else {
            $(this).attr("data-sort", "desc");
            sort = "desc";
          }
        }
        $(this).addClass(sort);
        console.log(type, sort);
        $("#sportsAgentPnlForm input[name='pageNo']").val(1);
        var len = $("#sportsAgentPnlForm .b-list").length - 1;
        var customerId = $($("#sportsAgentPnlForm .b-list")[len]).attr("data-customerid");
        tcg.ac.getSportsCopyAgentPnl(customerId, type, sort);
      });
  };
  tcg.ac.ui.searchElottCopyAgentPnl = function () {
    $(document)
      .off("click", "#elottAgentPnlForm .form-submit")
      .on("click", "#elottAgentPnlForm .form-submit", function (event) {
        $(".switchDecimal").removeClass("on").removeClass("off");
        if (event.originalEvent === undefined) {
          var len = $("#elottAgentPnlForm .b-list").length - 1;
          var customerId = $($("#elottAgentPnlForm .b-list")[len]).attr("data-customerid");
          if ($("#elottAgentPnlTable .divTableCell.sort").hasClass("asc")) {
            var type = $("#elottAgentPnlTable .divTableCell.sort.asc").attr("data-type");
            tcg.ac.getElottCopyAgentPnl(customerId, type, "asc");
          } else if ($("#elottAgentPnlTable .divTableCell.sort").hasClass("desc")) {
            var type = $("#elottAgentPnlTable .divTableCell.sort.desc").attr("data-type");
            tcg.ac.getElottCopyAgentPnl(customerId, type, "desc");
          } else {
            tcg.ac.getElottCopyAgentPnl(customerId);
          }
        } else {
          $("#elottAgentPnlForm .breadcrumbs .b-list.new-entry").remove();
          tcg.ac.getElottCopyAgentPnl();
        }
      });
  };
  tcg.ac.ui.loadElottCopyAgentPnl = function (result, summaryType, sortType, sortDir) {
    var _column = "";
    var currentUser = {};
    for (var i = 0; i < result.list.length; i++) {
      if (tcg.ac.customerId === +result.list[i].customerId) {
        currentUser = result.list[i];
        break;
      }
    }
    if (summaryType == 0) {
      _column += "<div class='divTableCell'>" + tcg.lib.prop("ac_rng_pnl_summaryType_0_column_1") + "</div>";
      // _column += "<div class='divTableCell'>" +tcg.lib.prop("ac_rng_pnl_summaryType_0_column_2")+ "</div>";
      // _column += "<div class='divTableCell'>" +tcg.lib.prop("ac_rng_pnl_summaryType_0_column_3")+ "</div>";
      _column +=
        "<div class='divTableCell tbl-link sort' data-sort='' data-type='betAmount'>" +
        tcg.lib.prop("ac_rng_pnl_summaryType_0_column_4") +
        "</div>";
      _column +=
        "<div class='divTableCell tbl-link sort' data-sort='' data-type='winAmount'>" +
        tcg.lib.prop("ac_rng_pnl_summaryType_0_column_5") +
        "</div>";
      _column += "<div class='divTableCell'>" + tcg.lib.prop("ac_rng_pnl_summaryType_0_column_6") + "</div>";
      _column += "<div class='divTableCell'>" + tcg.lib.prop("ac_rng_pnl_summaryType_0_column_7") + "</div>";
      if (currentUser.merchantCharge != undefined && currentUser.merchantCharge != 0) {
        _column +=
          "<div class='divTableCell tbl-link' data-sort='' data-type='merchantCharge'>" +
          tcg.lib.prop("ac_lotto_pnl_summaryType_0_column_11") +
          "</div>";
      }
      _column +=
        "<div class='divTableCell tbl-link sort' data-sort='' data-type='profitAndLoss'>" +
        tcg.lib.prop("ac_rng_pnl_summaryType_0_column_8") +
        "</div>";
    } else {
      _column += "<div class='divTableCell'>" + tcg.lib.prop("ac_rng_pnl_summaryType_1_column_1") + "</div>";
      // _column += "<div class='divTableCell'>" +tcg.lib.prop("ac_rng_pnl_summaryType_1_column_2")+ "</div>";
      // _column += "<div class='divTableCell'>" +tcg.lib.prop("ac_rng_pnl_summaryType_1_column_3")+ "</div>";
      _column +=
        "<div class='divTableCell tbl-link sort' data-sort='' data-type='betAmount'>" +
        tcg.lib.prop("ac_rng_pnl_summaryType_1_column_4") +
        "</div>";
      _column +=
        "<div class='divTableCell tbl-link sort' data-sort='' data-type='winAmount'>" +
        tcg.lib.prop("ac_rng_pnl_summaryType_1_column_5") +
        "</div>";
      _column += "<div class='divTableCell'>" + tcg.lib.prop("ac_rng_pnl_summaryType_1_column_6") + "</div>";
      _column += "<div class='divTableCell'>" + tcg.lib.prop("ac_rng_pnl_summaryType_1_column_7") + "</div>";
      if (currentUser.merchantCharge != undefined && currentUser.merchantCharge != 0) {
        _column +=
          "<div class='divTableCell tbl-link' data-sort='' data-type='merchantCharge'>" +
          tcg.lib.prop("ac_lotto_pnl_summaryType_1_column_11") +
          "</div>";
      }
      _column +=
        "<div class='divTableCell tbl-link sort' data-sort='' data-type='profitAndLoss'>" +
        tcg.lib.prop("ac_rng_pnl_summaryType_1_column_8") +
        "</div>";
    }
    $("#elottAgentPnlColumn").html(_column);

    var decimal = 2,
      list = result.list,
      _html = "";
    if (list.length > 0 || list.length != null) {
      for (var i = 0; i < list.length; i++) {
        _html += "<div class='divTableRow'>";
        var account = list[i].customerName;
        _html +=
          "<div class='divTableCell tbl-link ps-strings' data-customerId='" + account + "'>" + account + "</div>";
        // var total_deposit = list[i].deposit;
        // _html += "<div class='divTableCell ps-num' data-switchDecimal='" + decimal + "' data-value='" + total_deposit + "'>" + tcg.ac.customCurrencyFormat(total_deposit, decimal) + "</div>";
        // var total_withdraw = list[i].withdraw;
        // _html += "<div class='divTableCell ps-num' data-switchDecimal='" + decimal + "' data-value='" + total_withdraw + "'>" + tcg.ac.customCurrencyFormat(total_withdraw, decimal) + "</div>";
        var total_betting_loss = list[i].betAmount;
        _html +=
          "<div class='divTableCell' data-switchDecimal='" +
          decimal +
          "' data-value='" +
          total_betting_loss +
          "'>" +
          tcg.ac.customCurrencyFormat(total_betting_loss, decimal) +
          "</div>";
        var total_game_winnings = list[i].winAmount;
        var gameWinningColor = total_game_winnings;
        _html +=
          "<div class='divTableCell " +
          gameWinningColor +
          "'  data-switchDecimal='" +
          decimal +
          "' data-value='" +
          total_game_winnings +
          "'>" +
          tcg.ac.customCurrencyFormat(total_game_winnings, decimal) +
          "</div>";
        var total_game_rebate = list[i].rebate + list[i].agentCommission;
        _html +=
          "<div class='divTableCell' data-switchDecimal='" +
          decimal +
          "' data-value='" +
          total_game_rebate +
          "'>" +
          tcg.ac.customCurrencyFormat(total_game_rebate, decimal) +
          "</div>";
        var total_promotion = list[i].promotion;
        _html +=
          "<div class='divTableCell' data-switchDecimal='" +
          decimal +
          "' data-value='" +
          total_promotion +
          "'>" +
          tcg.ac.customCurrencyFormat(total_promotion, decimal) +
          "</div>";
        if (currentUser.merchantCharge != undefined && currentUser.merchantCharge != 0) {
          _html +=
            "<div class='divTableCell' data-switchDecimal='" +
            decimal +
            "' data-value='" +
            list[i].merchantCharge +
            "'>" +
            tcg.ac.customCurrencyFormat(list[i].merchantCharge, decimal) +
            "</div>";
        }
        var total_pnl = list[i].profitAndLoss;
        var profitLossColor = total_pnl >= 0 ? "tbl-red" : "tbl-green";
        _html +=
          "<div class='divTableCell " +
          profitLossColor +
          "' data-switchDecimal='" +
          decimal +
          "' data-value='" +
          total_pnl +
          "'>" +
          tcg.ac.customCurrencyFormat(total_pnl, decimal) +
          "</div>";
        _html += "</div>";
      }
    } else {
      _html += "<div class='tableContent-wrp'>";
      _html += "<div class='noResult-data'>" + tcg.lib.prop("no_result") + "</div>";
      _html += "</div>";
    }
    $("#elottAgentPnlList").html(_html);
    if (sortType && sortDir) {
      $("#elottAgentPnlTable .divTableCell.tbl-link.sort[data-type='" + sortType + "']")
        .attr("data-sort", sortDir)
        .addClass(sortDir);
    }
  };
  tcg.ac.ui.switchElottCopyAgentPnlSummaryType = function () {
    $(document)
      .off("click", "#elottAgentPnlForm .summaryTypeList li")
      .on("click", "#elottAgentPnlForm .summaryTypeList li", function () {
        if (!$(this).hasClass("active") && !$("#elottAgentPnlForm .form-submit").hasClass("processing")) {
          var rel = $(this).attr("data-rel");
          $("#elottAgentPnlForm .summaryTypeList li").removeClass("active");
          $(this).addClass("active");
          $("#elottAgentPnlTable .divTableCell.sort").attr("data-sort", "").removeClass("asc").removeClass("desc");
          $("#elottAgentPnlForm [name='pageNo']").val(1);
          $("#elottAgentPnlForm [name='summaryType']").val(rel);
          $("#elottAgentPnlForm [name='customerName']").val("");
          $("#elottAgentPnlForm .form-submit").click();
        }
      });
  };
  tcg.ac.ui.selectElottCopyAgentDownlinePnl = function () {
    $(document)
      .off("click", "#elottAgentPnlList .tbl-link")
      .on("click", "#elottAgentPnlList .tbl-link", function () {
        if (!$(this).hasClass("loading")) {
          var customerName = $(this).text(),
            customerId = $(this).attr("data-customerId"),
            isOpen = false;
          $("#elottAgentPnlForm .breadcrumbs .b-list").each(function () {
            if ($(this).attr("data-customerId") == customerId) {
              isOpen = true;
            }
          });
          if (!isOpen) {
            $("#elottAgentPnlForm .breadcrumbs .b-list").addClass("loading");
            $("#elottAgentPnlForm .tbl-link").addClass("loading");
            var _html =
              "<div class='arrow-small-con mem-icon2 inline-block b-list new-entry' data-customerId='" +
              customerId +
              "'>" +
              customerName +
              "</div>";
            $("#elottAgentPnlForm .breadcrumbs").append(_html);
          }
          $("#elottAgentPnlTable .divTableCell.sort").attr("data-sort", "").removeClass("asc").removeClass("desc");
          $("#elottAgentPnlForm [name='pageNo']").val(1);
          //$("#elottAgentPnlForm [name='customerName']").val( customerName == window.sessionStorage.getItem("username") ? "" : customerName );
          tcg.ac.getElottCopyAgentPnl(customerId);
        }
      });
  };
  tcg.ac.ui.clickElottCopyAgentPnlBreadcrumbs = function () {
    $(document)
      .off("click", "#elottAgentPnlForm .breadcrumbs .b-list")
      .on("click", "#elottAgentPnlForm .breadcrumbs .b-list", function () {
        if (!$(this).hasClass("loading")) {
          var customerId = $(this).attr("data-customerId"),
            customerName = $(this).text(),
            removeFromList = false;
          $("#elottAgentPnlForm .breadcrumbs .b-list").each(function () {
            if (removeFromList) $(this).remove();
            if ($(this).attr("data-customerId") == customerId) {
              removeFromList = true;
            }
          });
          $("#elottAgentPnlTable .divTableCell.sort").attr("data-sort", "").removeClass("asc").removeClass("desc");
          $("#elottAgentPnlForm [name='pageNo']").val(1);
          $("#elottAgentPnlForm .breadcrumbs .b-list").addClass("loading");
          $("#elottAgentPnlList .tbl-link").addClass("loading");
          //$("#elottAgentPnlForm [name='customerName']").val( customerName == window.sessionStorage.getItem("username") ? "" : customerName );
          tcg.ac.getElottCopyAgentPnl(customerId);
        }
      });
  };
  tcg.ac.ui.sortElottCopyAgentPnl = function () {
    $(document)
      .off("click", "#elottAgentPnlTable .divTableCell.sort")
      .on("click", "#elottAgentPnlTable .divTableCell.sort", function (event) {
        var type = $(this).attr("data-type");
        var sort = $(this).attr("data-sort");
        $("#elottAgentPnlTable .divTableCell.sort").addClass("loading");
        $("#elottAgentPnlTable .divTableCell.sort").removeClass("asc").removeClass("desc");
        $("#elottAgentPnlTable .divTableCell.sort").attr("data-sort", "");
        if (sort === "") {
          $(this).attr("data-sort", "desc");
          sort = "desc";
        } else {
          if (sort == "desc") {
            $(this).attr("data-sort", "asc");
            sort = "asc";
          } else {
            $(this).attr("data-sort", "desc");
            sort = "desc";
          }
        }
        $(this).addClass(sort);
        $("#elottAgentPnlForm input[name='pageNo']").val(1);
        var len = $("#elottAgentPnlForm .b-list").length - 1;
        var customerId = $($("#elottAgentPnlForm .b-list")[len]).attr("data-customerid");
        tcg.ac.getElottCopyAgentPnl(customerId, type, sort);
      });
  };

  tcg.ac.ui.appendBreadCrumbsPalStatements = function (data) {
    if (data) {
      var _html = "";
      for (var i = 0; data.length > i; i++) {
        var agent_new_entry = i == 0 ? "agentName" : "new-entry";
        _html +=
          "<div class='arrow-small-con mem-icon2 inline-block b-list " +
          agent_new_entry +
          " ' data-customerId='" +
          data[i].customerName +
          "'>" +
          data[i].customerName +
          "</div>";
      }
      $(".palStatements .breadcrumbs").html(_html);
    }
  };

  // Lotto PNL Functions
  tcg.ac.ui.searchLottoAgentPnl = function () {
    $(document)
      .off("click", "#lottoAgentPnlForm .form-submit")
      .on("click", "#lottoAgentPnlForm .form-submit", function (event) {
        if (event.originalEvent === undefined) {
          var len = $("#lottoAgentPnlForm .b-list").length - 1;
          var customerId = $($(".b-list")[len]).attr("data-customerid");
          tcg.ac.getLottoAgentPnl(customerId);
        } else {
          $("#lottoAgentPnlForm .breadcrumbs .b-list.new-entry").remove();
          tcg.ac.getLottoAgentPnl();
        }
      });
  };
  tcg.ac.ui.loadLottoAgentPnl = function (result, summaryType) {
    var _column = "";
    if (summaryType == 0) {
      _column +=
        "<div class='divTableCell onel-th-x'>" + tcg.lib.prop("ac_lotto_pnl_summaryType_0_column_1") + "</div>";
      _column +=
        "<div class='divTableCell onel-th-x'>" + tcg.lib.prop("ac_lotto_pnl_summaryType_0_column_2") + "</div>";
      _column +=
        "<div class='divTableCell onel-th-x'>" + tcg.lib.prop("ac_lotto_pnl_summaryType_0_column_3") + "</div>";
      _column +=
        "<div class='divTableCell onel-th-x'>" + tcg.lib.prop("ac_lotto_pnl_summaryType_0_column_4") + "</div>";
      _column +=
        "<div class='divTableCell onel-th-x'>" + tcg.lib.prop("ac_lotto_pnl_summaryType_0_column_5") + "</div>";
      _column +=
        "<div class='divTableCell onel-th-x'>" + tcg.lib.prop("ac_lotto_pnl_summaryType_0_column_6") + "</div>";
      _column +=
        "<div class='divTableCell onel-th-x'>" + tcg.lib.prop("ac_lotto_pnl_summaryType_0_column_7") + "</div>";
      _column +=
        "<div class='divTableCell onel-th-x'>" + tcg.lib.prop("ac_lotto_pnl_summaryType_0_column_8") + "</div>";
    } else {
      _column +=
        "<div class='divTableCell onel-th-x'>" + tcg.lib.prop("ac_lotto_pnl_summaryType_1_column_1") + "</div>";
      _column +=
        "<div class='divTableCell onel-th-x'>" + tcg.lib.prop("ac_lotto_pnl_summaryType_1_column_2") + "</div>";
      _column +=
        "<div class='divTableCell onel-th-x'>" + tcg.lib.prop("ac_lotto_pnl_summaryType_1_column_3") + "</div>";
      _column +=
        "<div class='divTableCell onel-th-x'>" + tcg.lib.prop("ac_lotto_pnl_summaryType_1_column_4") + "</div>";
      _column +=
        "<div class='divTableCell onel-th-x'>" + tcg.lib.prop("ac_lotto_pnl_summaryType_1_column_5") + "</div>";
      _column +=
        "<div class='divTableCell onel-th-x'>" + tcg.lib.prop("ac_lotto_pnl_summaryType_1_column_6") + "</div>";
      _column +=
        "<div class='divTableCell onel-th-x'>" + tcg.lib.prop("ac_lotto_pnl_summaryType_1_column_7") + "</div>";
      _column +=
        "<div class='divTableCell onel-th-x'>" + tcg.lib.prop("ac_lotto_pnl_summaryType_1_column_8") + "</div>";
    }
    $("#lottoAgentPnlColumn").html(_column);

    var decimal = 2,
      list = result.list,
      _html = "";
    for (var i = 0; i < list.length; i++) {
      _html += "<div class='divTableRow'>";
      var account = list[i].customer_name.split("@");
      _html +=
        "<div class='divTableCell tbl-link ps-strings' data-customerId='" +
        list[i].customer_id +
        "'>" +
        account[1] +
        "</div>";
      var depositAndTransferIn = list[i].deposit + list[i].agent_transfer_in;
      _html +=
        "<div class='divTableCell ps-num' data-switchDecimal='" +
        decimal +
        "' data-value='" +
        list[i].deposit +
        "'>" +
        tcg.ac.customCurrencyFormat(depositAndTransferIn, decimal) +
        "</div>";
      var withdrawAndTransferOut = list[i].withdraw + list[i].agent_transfer_out;
      _html +=
        "<div class='divTableCell ps-num' data-switchDecimal='" +
        decimal +
        "' data-value='" +
        list[i].withdraw +
        "'>" +
        tcg.ac.customCurrencyFormat(withdrawAndTransferOut, decimal) +
        "</div>";
      _html +=
        "<div class='divTableCell ps-num' data-switchDecimal='" +
        decimal +
        "' data-value='" +
        list[i].lott_game_betting +
        "'>" +
        tcg.ac.customCurrencyFormat(list[i].lott_game_betting, decimal) +
        "</div>";
      var lottoGameRebate = list[i].lott_game_rebate + list[i].lott_agent_commission;
      var lottGameRebateColor = lottoGameRebate;
      _html +=
        "<div class='divTableCell ps-num " +
        lottGameRebateColor +
        "'  data-switchDecimal='" +
        decimal +
        "' data-value='" +
        lottoGameRebate +
        "'>" +
        tcg.ac.customCurrencyFormat(lottoGameRebate, decimal) +
        "</div>";
      _html +=
        "<div class='divTableCell ps-num' data-switchDecimal='" +
        decimal +
        "' data-value='" +
        list[i].lott_game_winning +
        "'>" +
        tcg.ac.customCurrencyFormat(list[i].lott_game_winning, decimal) +
        "</div>";
      var promobrokeincome = list[i].lott_promotion + list[i].lott_daily_income + list[i].lott_brokerage;
      _html +=
        "<div class='divTableCell ps-num' data-switchDecimal='" +
        decimal +
        "' data-value='" +
        promobrokeincome +
        "'>" +
        tcg.ac.customCurrencyFormat(promobrokeincome, decimal) +
        "</div>";
      var profitLossColor = list[i].lott_game_pnl >= 0 ? "tbl-red" : "tbl-green";
      _html +=
        "<div class='divTableCell ps-num " +
        profitLossColor +
        "' data-switchDecimal='" +
        decimal +
        "' data-value='" +
        list[i].lott_game_pnl +
        "'>" +
        tcg.ac.customCurrencyFormat(list[i].lott_game_pnl, decimal) +
        "</div>";
      _html += "</div>";
    }
    $("#lottoAgentPnlList").html(_html);
  };
  tcg.ac.ui.switchLottoAgentPnlSummaryType = function () {
    $(document)
      .off("click", "#lottoAgentPnlForm .summaryTypeList li")
      .on("click", "#lottoAgentPnlForm .summaryTypeList li", function () {
        if (!$(this).hasClass("active") && !$("#lottoAgentPnlForm .form-submit").hasClass("processing")) {
          var rel = $(this).attr("data-rel");
          $("#lottoAgentPnlForm .summaryTypeList li").removeClass("active");
          $(this).addClass("active");
          $("#lottoAgentPnlForm [name='pageNo']").val(1);
          $("#lottoAgentPnlForm [name='summaryType']").val(rel);
          $("#lottoAgentPnlForm .form-submit").click();
        }
      });
  };
  tcg.ac.ui.selectLottoAgentDownlinePnl = function () {
    $(document)
      .off("click", "#lottoAgentPnlList .tbl-link")
      .on("click", "#lottoAgentPnlList .tbl-link", function () {
        if (!$(this).hasClass("loading")) {
          var customerName = $(this).text(),
            customerId = $(this).attr("data-customerId"),
            isOpen = false;
          $("#lottoAgentPnlForm .breadcrumbs .b-list").each(function () {
            if ($(this).attr("data-customerId") == customerId) {
              isOpen = true;
            }
          });
          if (!isOpen) {
            $("#lottoAgentPnlForm .breadcrumbs .b-list").addClass("loading");
            $("#lottoAgentPnlForm .summaryTypeList li").addClass("loading");
            _html =
              "<div class='arrow-small-con mem-icon2 inline-block b-list new-entry' data-customerId='" +
              customerId +
              "'>" +
              customerName +
              "</div>";
            $("#lottoAgentPnlForm .breadcrumbs").append(_html);
          }
          $("#lottoAgentPnlForm [name='pageNo']").val(1);
          tcg.ac.getLottoAgentPnl(customerId);
        }
      });
  };
  tcg.ac.ui.clickLottoAgentPnlBreadcrumbs = function () {
    $(document)
      .off("click", "#lottoAgentPnlForm .breadcrumbs .b-list")
      .on("click", "#lottoAgentPnlForm .breadcrumbs .b-list", function () {
        if (!$(this).hasClass("loading")) {
          var customerId = $(this).attr("data-customerId"),
            customerName = $(this).text(),
            removeFromList = false;
          $("#lottoAgentPnlForm .breadcrumbs .b-list").each(function () {
            if (removeFromList) $(this).remove();
            if ($(this).attr("data-customerId") == customerId) {
              removeFromList = true;
            }
          });
          $("#lottoAgentPnlForm [name='pageNo']").val(1);
          $("#lottoAgentPnlForm .breadcrumbs .b-list").addClass("loading");
          $("#lottoAgentPnlForm .summaryTypeList li").addClass("loading");
          tcg.ac.getLottoAgentPnl(customerId);
        }
      });
  };
  //
  tcg.ac.ui.searchFishAgentPnl = function () {
    $(document)
      .off("click", "#fishAgentPnlForm .form-submit")
      .on("click", "#fishAgentPnlForm .form-submit", function () {
        $("#fishAgentPnlForm .breadcrumbs .b-list.new--entry").remove();
        tcg.ac.getFishAgentPnl();
      });
  };
  tcg.ac.ui.searchSportsAgentPnl = function () {
    $(document)
      .off("click", "#sportsAgentPnlForm .form-submit")
      .on("click", "#sportsAgentPnlForm .form-submit", function () {
        $("#sportsAgentPnlForm .breadcrumbs .b-list.new--entry").remove();
        tcg.ac.getSportsAgentPnl();
      });
  };
  tcg.ac.ui.searchPvpAgentPnl = function () {
    $(document)
      .off("click", "#pvpAgentPnlForm .form-submit")
      .on("click", "#pvpAgentPnlForm .form-submit", function (event) {
        //$("#pvpAgentPnlForm .breadcrumbs .b-list.new-entry").remove();
        //tcg.ac.getPvpAgentPnl();

        if (event.originalEvent === undefined) {
          var len = $("#pvpAgentPnlForm .b-list").length;
          var customerId = $($(".b-list")[len]).attr("data-customerid");
          tcg.ac.getPvpAgentPnl(customerId);
        } else {
          $("#pvpAgentPnlForm .breadcrumbs .b-list.new-entry").remove();
          tcg.ac.getPvpAgentPnl();
        }
      });
  };
  tcg.ac.ui.searchRNGAgentPnl = function () {
    $(document)
      .off("click", "#rngAgentPnlForm .form-submit")
      .on("click", "#rngAgentPnlForm .form-submit", function (event) {
        if (event.originalEvent === undefined) {
          var len = $("#rngAgentPnlForm .b-list").length - 1;
          var customerId = $($("#rngAgentPnlForm .breadcrumbs .b-list")[len]).attr("data-customerid");
          tcg.ac.getRNGAgentPnl(customerId);
        } else {
          $("#rngAgentPnlForm .breadcrumbs .b-list.new-entry").remove();
          tcg.ac.getRNGAgentPnl();
        }
      });
  };
  tcg.ac.ui.loadPvpAgentPnl = function (result, summaryType) {
    var _column = "";
    if (summaryType == 0) {
      _column += "<div class='divTableCell onel-th-x'>" + tcg.lib.prop("ac_pvp_pnl_summaryType_0_column_1") + "</div>";
      _column += "<div class='divTableCell onel-th-x'>" + tcg.lib.prop("ac_pvp_pnl_summaryType_0_column_2") + "</div>";
      _column += "<div class='divTableCell onel-th-x'>" + tcg.lib.prop("ac_pvp_pnl_summaryType_0_column_3") + "</div>";
      _column += "<div class='divTableCell onel-th-x'>" + tcg.lib.prop("ac_pvp_pnl_summaryType_0_column_4") + "</div>";
      _column += "<div class='divTableCell onel-th-x'>" + tcg.lib.prop("ac_pvp_pnl_summaryType_0_column_5") + "</div>";
      _column += "<div class='divTableCell onel-th-x'>" + tcg.lib.prop("ac_pvp_pnl_summaryType_0_column_6") + "</div>";
      _column += "<div class='divTableCell onel-th-x'>" + tcg.lib.prop("ac_pvp_pnl_summaryType_0_column_7") + "</div>";
      _column += "<div class='divTableCell onel-th-x'>" + tcg.lib.prop("ac_pvp_pnl_summaryType_0_column_8") + "</div>";
    } else {
      _column += "<div class='divTableCell onel-th-x'>" + tcg.lib.prop("ac_pvp_pnl_summaryType_1_column_1") + "</div>";
      _column += "<div class='divTableCell onel-th-x'>" + tcg.lib.prop("ac_pvp_pnl_summaryType_1_column_2") + "</div>";
      _column += "<div class='divTableCell onel-th-x'>" + tcg.lib.prop("ac_pvp_pnl_summaryType_1_column_3") + "</div>";
      _column += "<div class='divTableCell onel-th-x'>" + tcg.lib.prop("ac_pvp_pnl_summaryType_1_column_4") + "</div>";
      _column += "<div class='divTableCell onel-th-x'>" + tcg.lib.prop("ac_pvp_pnl_summaryType_1_column_5") + "</div>";
      _column += "<div class='divTableCell onel-th-x'>" + tcg.lib.prop("ac_pvp_pnl_summaryType_1_column_6") + "</div>";
      _column += "<div class='divTableCell onel-th-x'>" + tcg.lib.prop("ac_pvp_pnl_summaryType_1_column_7") + "</div>";
      _column += "<div class='divTableCell onel-th-x'>" + tcg.lib.prop("ac_pvp_pnl_summaryType_1_column_8") + "</div>";
    }
    $("#pvpAgentPnlColumn").html(_column);

    var decimal = 2,
      list = result.list,
      _html = "";
    if (list.length > 0) {
      for (var i = 0; i < list.length; i++) {
        _html += "<div class='divTableRow'>";
        var account = list[i].customer_name.split("@");
        _html +=
          "<div class='divTableCell tbl-link ps-strings' data-customerId='" +
          list[i].customer_id +
          "'>" +
          account[1] +
          "</div>";
        var total_deposit = list[i].deposit;
        _html +=
          "<div class='divTableCell ps-num' data-switchDecimal='" +
          decimal +
          "' data-value='" +
          total_deposit +
          "'>" +
          tcg.ac.customCurrencyFormat(total_deposit, decimal) +
          "</div>";
        var total_withdraw = list[i].withdraw;
        _html +=
          "<div class='divTableCell ps-num' data-switchDecimal='" +
          decimal +
          "' data-value='" +
          total_withdraw +
          "'>" +
          tcg.ac.customCurrencyFormat(total_withdraw, decimal) +
          "</div>";
        var total_betting_loss = list[i].pvp_game_loses;
        _html +=
          "<div class='divTableCell ps-num' data-switchDecimal='" +
          decimal +
          "' data-value='" +
          total_betting_loss +
          "'>" +
          tcg.ac.customCurrencyFormat(total_betting_loss, decimal) +
          "</div>";
        var total_game_winnings = list[i].pvp_game_winnings;
        var gameWinningColor = total_game_winnings;
        _html +=
          "<div class='divTableCell ps-num " +
          gameWinningColor +
          "'  data-switchDecimal='" +
          decimal +
          "' data-value='" +
          total_game_winnings +
          "'>" +
          tcg.ac.customCurrencyFormat(total_game_winnings, decimal) +
          "</div>";
        var total_game_rebate = list[i].pvp_game_rebate;
        _html +=
          "<div class='divTableCell ps-num' data-switchDecimal='" +
          decimal +
          "' data-value='" +
          total_game_rebate +
          "'>" +
          tcg.ac.customCurrencyFormat(total_game_rebate, decimal) +
          "</div>";
        var total_promotion = list[i].pvp_promotion;
        _html +=
          "<div class='divTableCell ps-num' data-switchDecimal='" +
          decimal +
          "' data-value='" +
          total_promotion +
          "'>" +
          tcg.ac.customCurrencyFormat(total_promotion, decimal) +
          "</div>";
        var total_pnl = list[i].pvp_game_pnl;
        var profitLossColor = total_pnl >= 0 ? "tbl-red" : "tbl-green";
        _html +=
          "<div class='divTableCell ps-num " +
          profitLossColor +
          "' data-switchDecimal='" +
          decimal +
          "' data-value='" +
          total_pnl +
          "'>" +
          tcg.ac.customCurrencyFormat(total_pnl, decimal) +
          "</div>";
        _html += "</div>";
      }
    } else {
      _html += "<div class='tableContent-wrp'>";
      _html += "<div class='noResult-data'>" + tcg.lib.prop("no_result") + "</div>";
      _html += "</div>";
    }
    $("#pvpAgentPnlList").html(_html);
  };
  tcg.ac.ui.loadRNGAgentPnl = function (result, summaryType) {
    var _column = "";
    if (summaryType == 0) {
      _column += "<div class='divTableCell onel-th-x'>" + tcg.lib.prop("ac_rng_pnl_summaryType_0_column_1") + "</div>";
      _column += "<div class='divTableCell onel-th-x'>" + tcg.lib.prop("ac_rng_pnl_summaryType_0_column_2") + "</div>";
      _column += "<div class='divTableCell onel-th-x'>" + tcg.lib.prop("ac_rng_pnl_summaryType_0_column_3") + "</div>";
      _column += "<div class='divTableCell onel-th-x'>" + tcg.lib.prop("ac_rng_pnl_summaryType_0_column_4") + "</div>";
      _column += "<div class='divTableCell onel-th-x'>" + tcg.lib.prop("ac_rng_pnl_summaryType_0_column_5") + "</div>";
      _column += "<div class='divTableCell onel-th-x'>" + tcg.lib.prop("ac_rng_pnl_summaryType_0_column_6") + "</div>";
      _column += "<div class='divTableCell onel-th-x'>" + tcg.lib.prop("ac_rng_pnl_summaryType_0_column_7") + "</div>";
      _column += "<div class='divTableCell onel-th-x'>" + tcg.lib.prop("ac_rng_pnl_summaryType_0_column_8") + "</div>";
    } else {
      _column += "<div class='divTableCell onel-th-x'>" + tcg.lib.prop("ac_rng_pnl_summaryType_1_column_1") + "</div>";
      _column += "<div class='divTableCell onel-th-x'>" + tcg.lib.prop("ac_rng_pnl_summaryType_1_column_2") + "</div>";
      _column += "<div class='divTableCell onel-th-x'>" + tcg.lib.prop("ac_rng_pnl_summaryType_1_column_3") + "</div>";
      _column += "<div class='divTableCell onel-th-x'>" + tcg.lib.prop("ac_rng_pnl_summaryType_1_column_4") + "</div>";
      _column += "<div class='divTableCell onel-th-x'>" + tcg.lib.prop("ac_rng_pnl_summaryType_1_column_5") + "</div>";
      _column += "<div class='divTableCell onel-th-x'>" + tcg.lib.prop("ac_rng_pnl_summaryType_1_column_6") + "</div>";
      _column += "<div class='divTableCell onel-th-x'>" + tcg.lib.prop("ac_rng_pnl_summaryType_1_column_7") + "</div>";
      _column += "<div class='divTableCell onel-th-x'>" + tcg.lib.prop("ac_rng_pnl_summaryType_1_column_8") + "</div>";
    }
    $("#rngAgentPnlColumn").html(_column);

    var decimal = 2,
      list = result.list,
      _html = "";
    if (list.length > 0 || list.length != null) {
      for (var i = 0; i < list.length; i++) {
        _html += "<div class='divTableRow'>";
        var account = list[i].customer_name.split("@");
        _html +=
          "<div class='divTableCell tbl-link ps-strings' data-customerId='" +
          list[i].customer_id +
          "'>" +
          account[1] +
          "</div>";
        var total_deposit = list[i].deposit;
        _html +=
          "<div class='divTableCell ps-num' data-switchDecimal='" +
          decimal +
          "' data-value='" +
          total_deposit +
          "'>" +
          tcg.ac.customCurrencyFormat(total_deposit, decimal) +
          "</div>";
        var total_withdraw = list[i].withdraw;
        _html +=
          "<div class='divTableCell ps-num' data-switchDecimal='" +
          decimal +
          "' data-value='" +
          total_withdraw +
          "'>" +
          tcg.ac.customCurrencyFormat(total_withdraw, decimal) +
          "</div>";
        var total_betting_loss = list[i].rng_game_betting + list[i].live_game_betting;
        _html +=
          "<div class='divTableCell ps-num' data-switchDecimal='" +
          decimal +
          "' data-value='" +
          total_betting_loss +
          "'>" +
          tcg.ac.customCurrencyFormat(total_betting_loss, decimal) +
          "</div>";
        var total_game_winnings = list[i].rng_game_winning + list[i].live_game_winning;
        var gameWinningColor = total_game_winnings;
        _html +=
          "<div class='divTableCell ps-num " +
          gameWinningColor +
          "'  data-switchDecimal='" +
          decimal +
          "' data-value='" +
          total_game_winnings +
          "'>" +
          tcg.ac.customCurrencyFormat(total_game_winnings, decimal) +
          "</div>";
        var total_game_rebate = list[i].rng_game_rebate + list[i].live_game_rebate;
        _html +=
          "<div class='divTableCell ps-num' data-switchDecimal='" +
          decimal +
          "' data-value='" +
          total_game_rebate +
          "'>" +
          tcg.ac.customCurrencyFormat(total_game_rebate, decimal) +
          "</div>";
        var total_promotion = list[i].rng_promotion;
        _html +=
          "<div class='divTableCell ps-num' data-switchDecimal='" +
          decimal +
          "' data-value='" +
          total_promotion +
          "'>" +
          tcg.ac.customCurrencyFormat(total_promotion, decimal) +
          "</div>";
        var total_pnl = list[i].rng_game_pnl + list[i].live_game_pnl;
        var profitLossColor = total_pnl >= 0 ? "tbl-red" : "tbl-green";
        _html +=
          "<div class='divTableCell ps-num " +
          profitLossColor +
          "' data-switchDecimal='" +
          decimal +
          "' data-value='" +
          total_pnl +
          "'>" +
          tcg.ac.customCurrencyFormat(total_pnl, decimal) +
          "</div>";
        _html += "</div>";
      }
    } else {
      _html += "<div class='tableContent-wrp'>";
      _html += "<div class='noResult-data'>" + tcg.lib.prop("no_result") + "</div>";
      _html += "</div>";
    }
    $("#rngAgentPnlList").html(_html);
  };
  tcg.ac.ui.loadFishAgentPnl = function (result, summaryType) {
    var _column = "";
    if (summaryType == 0) {
      _column += "<div class='divTableCell onel-th-x'>" + tcg.lib.prop("ac_fish_pnl_summaryType_0_column_1") + "</div>";
      _column += "<div class='divTableCell onel-th-x'>" + tcg.lib.prop("ac_fish_pnl_summaryType_0_column_2") + "</div>";
      _column += "<div class='divTableCell onel-th-x'>" + tcg.lib.prop("ac_fish_pnl_summaryType_0_column_3") + "</div>";
      _column += "<div class='divTableCell onel-th-x'>" + tcg.lib.prop("ac_fish_pnl_summaryType_0_column_4") + "</div>";
      _column += "<div class='divTableCell onel-th-x'>" + tcg.lib.prop("ac_fish_pnl_summaryType_0_column_5") + "</div>";
      _column += "<div class='divTableCell onel-th-x'>" + tcg.lib.prop("ac_fish_pnl_summaryType_0_column_6") + "</div>";
      _column += "<div class='divTableCell onel-th-x'>" + tcg.lib.prop("ac_fish_pnl_summaryType_0_column_7") + "</div>";
      _column += "<div class='divTableCell onel-th-x'>" + tcg.lib.prop("ac_fish_pnl_summaryType_0_column_8") + "</div>";
    } else {
      _column += "<div class='divTableCell onel-th-x'>" + tcg.lib.prop("ac_fish_pnl_summaryType_1_column_1") + "</div>";
      _column += "<div class='divTableCell onel-th-x'>" + tcg.lib.prop("ac_fish_pnl_summaryType_1_column_2") + "</div>";
      _column += "<div class='divTableCell onel-th-x'>" + tcg.lib.prop("ac_fish_pnl_summaryType_1_column_3") + "</div>";
      _column += "<div class='divTableCell onel-th-x'>" + tcg.lib.prop("ac_fish_pnl_summaryType_1_column_4") + "</div>";
      _column += "<div class='divTableCell onel-th-x'>" + tcg.lib.prop("ac_fish_pnl_summaryType_1_column_5") + "</div>";
      _column += "<div class='divTableCell onel-th-x'>" + tcg.lib.prop("ac_fish_pnl_summaryType_1_column_6") + "</div>";
      _column += "<div class='divTableCell onel-th-x'>" + tcg.lib.prop("ac_fish_pnl_summaryType_1_column_7") + "</div>";
      _column += "<div class='divTableCell onel-th-x'>" + tcg.lib.prop("ac_fish_pnl_summaryType_1_column_8") + "</div>";
    }
    $("#fishAgentPnlColumn").html(_column);

    var decimal = 2,
      list = result.list,
      _html = "";
    if (list.length > 0 || list.length != null) {
      for (var i = 0; i < list.length; i++) {
        _html += "<div class='divTableRow'>";
        var account = list[i].customer_name.split("@");
        _html +=
          "<div class='divTableCell tbl-link ps-strings' data-customerId='" +
          list[i].customer_id +
          "'>" +
          account[1] +
          "</div>";
        var total_deposit = list[i].deposit;
        _html +=
          "<div class='divTableCell ps-num' data-switchDecimal='" +
          decimal +
          "' data-value='" +
          total_deposit +
          "'>" +
          tcg.ac.customCurrencyFormat(total_deposit, decimal) +
          "</div>";
        var total_withdraw = list[i].withdraw;
        _html +=
          "<div class='divTableCell ps-num' data-switchDecimal='" +
          decimal +
          "' data-value='" +
          total_withdraw +
          "'>" +
          tcg.ac.customCurrencyFormat(total_withdraw, decimal) +
          "</div>";
        var total_betting_loss = list[i].fish_game_betting;
        _html +=
          "<div class='divTableCell ps-num' data-switchDecimal='" +
          decimal +
          "' data-value='" +
          total_betting_loss +
          "'>" +
          tcg.ac.customCurrencyFormat(total_betting_loss, decimal) +
          "</div>";
        var total_game_winnings = list[i].fish_game_winning;
        var gameWinningColor = total_game_winnings;
        _html +=
          "<div class='divTableCell ps-num " +
          gameWinningColor +
          "'  data-switchDecimal='" +
          decimal +
          "' data-value='" +
          total_game_winnings +
          "'>" +
          tcg.ac.customCurrencyFormat(total_game_winnings, decimal) +
          "</div>";
        var total_game_rebate = list[i].fish_game_rebate;
        _html +=
          "<div class='divTableCell ps-num' data-switchDecimal='" +
          decimal +
          "' data-value='" +
          total_game_rebate +
          "'>" +
          tcg.ac.customCurrencyFormat(total_game_rebate, decimal) +
          "</div>";
        var total_promotion = 0; //fish无活动，默认为0
        _html +=
          "<div class='divTableCell ps-num' data-switchDecimal='" +
          decimal +
          "' data-value='" +
          total_promotion +
          "'>" +
          tcg.ac.customCurrencyFormat(total_promotion, decimal) +
          "</div>";
        var total_pnl = list[i].fish_game_pnl;
        var profitLossColor = total_pnl >= 0 ? "tbl-red" : "tbl-green";
        _html +=
          "<div class='divTableCell ps-num " +
          profitLossColor +
          "' data-switchDecimal='" +
          decimal +
          "' data-value='" +
          total_pnl +
          "'>" +
          tcg.ac.customCurrencyFormat(total_pnl, decimal) +
          "</div>";
        _html += "</div>";
      }
    } else {
      _html += "<div class='tableContent-wrp'>";
      _html += "<div class='noResult-data'>" + tcg.lib.prop("no_result") + "</div>";
      _html += "</div>";
    }
    $("#fishAgentPnlList").html(_html);
  };
  tcg.ac.ui.loadSportsAgentPnl = function (result, summaryType) {
    var _column = "";
    if (summaryType == 0) {
      _column +=
        "<div class='divTableCell onel-th-x'>" + tcg.lib.prop("ac_sports_pnl_summaryType_0_column_1") + "</div>";
      _column +=
        "<div class='divTableCell onel-th-x'>" + tcg.lib.prop("ac_sports_pnl_summaryType_0_column_2") + "</div>";
      _column +=
        "<div class='divTableCell onel-th-x'>" + tcg.lib.prop("ac_sports_pnl_summaryType_0_column_3") + "</div>";
      _column +=
        "<div class='divTableCell onel-th-x'>" + tcg.lib.prop("ac_sports_pnl_summaryType_0_column_4") + "</div>";
      _column +=
        "<div class='divTableCell onel-th-x'>" + tcg.lib.prop("ac_sports_pnl_summaryType_0_column_5") + "</div>";
      _column +=
        "<div class='divTableCell onel-th-x'>" + tcg.lib.prop("ac_sports_pnl_summaryType_0_column_6") + "</div>";
      _column +=
        "<div class='divTableCell onel-th-x'>" + tcg.lib.prop("ac_sports_pnl_summaryType_0_column_7") + "</div>";
      _column +=
        "<div class='divTableCell onel-th-x'>" + tcg.lib.prop("ac_sports_pnl_summaryType_0_column_8") + "</div>";
    } else {
      _column +=
        "<div class='divTableCell onel-th-x'>" + tcg.lib.prop("ac_sports_pnl_summaryType_1_column_1") + "</div>";
      _column +=
        "<div class='divTableCell onel-th-x'>" + tcg.lib.prop("ac_sports_pnl_summaryType_1_column_2") + "</div>";
      _column +=
        "<div class='divTableCell onel-th-x'>" + tcg.lib.prop("ac_sports_pnl_summaryType_1_column_3") + "</div>";
      _column +=
        "<div class='divTableCell onel-th-x'>" + tcg.lib.prop("ac_sports_pnl_summaryType_1_column_4") + "</div>";
      _column +=
        "<div class='divTableCell onel-th-x'>" + tcg.lib.prop("ac_sports_pnl_summaryType_1_column_5") + "</div>";
      _column +=
        "<div class='divTableCell onel-th-x'>" + tcg.lib.prop("ac_sports_pnl_summaryType_1_column_6") + "</div>";
      _column +=
        "<div class='divTableCell onel-th-x'>" + tcg.lib.prop("ac_sports_pnl_summaryType_1_column_7") + "</div>";
      _column +=
        "<div class='divTableCell onel-th-x'>" + tcg.lib.prop("ac_sports_pnl_summaryType_1_column_8") + "</div>";
    }
    $("#sportsAgentPnlColumn").html(_column);

    var decimal = 2,
      list = result.list,
      _html = "";
    if (list.length > 0 || list.length != null) {
      for (var i = 0; i < list.length; i++) {
        _html += "<div class='divTableRow'>";
        var account = list[i].customer_name.split("@");
        _html +=
          "<div class='divTableCell tbl-link ps-strings' data-customerId='" +
          list[i].customer_id +
          "'>" +
          account[1] +
          "</div>";
        var total_deposit = list[i].deposit;
        _html +=
          "<div class='divTableCell ps-num' data-switchDecimal='" +
          decimal +
          "' data-value='" +
          total_deposit +
          "'>" +
          tcg.ac.customCurrencyFormat(total_deposit, decimal) +
          "</div>";
        var total_withdraw = list[i].withdraw;
        _html +=
          "<div class='divTableCell ps-num' data-switchDecimal='" +
          decimal +
          "' data-value='" +
          total_withdraw +
          "'>" +
          tcg.ac.customCurrencyFormat(total_withdraw, decimal) +
          "</div>";
        var total_betting_loss = list[i].sports_game_betting;
        _html +=
          "<div class='divTableCell ps-num' data-switchDecimal='" +
          decimal +
          "' data-value='" +
          total_betting_loss +
          "'>" +
          tcg.ac.customCurrencyFormat(total_betting_loss, decimal) +
          "</div>";
        var total_game_winnings = list[i].sports_game_winning;
        var gameWinningColor = total_game_winnings;
        _html +=
          "<div class='divTableCell ps-num " +
          gameWinningColor +
          "'  data-switchDecimal='" +
          decimal +
          "' data-value='" +
          total_game_winnings +
          "'>" +
          tcg.ac.customCurrencyFormat(total_game_winnings, decimal) +
          "</div>";
        var total_game_rebate = list[i].sports_agent_commission + list[i].sports_game_rebate;
        _html +=
          "<div class='divTableCell ps-num' data-switchDecimal='" +
          decimal +
          "' data-value='" +
          total_game_rebate +
          "'>" +
          tcg.ac.customCurrencyFormat(total_game_rebate, decimal) +
          "</div>";
        var total_promotion = 0; //sports无活动，默认为0
        _html +=
          "<div class='divTableCell ps-num' data-switchDecimal='" +
          decimal +
          "' data-value='" +
          total_promotion +
          "'>" +
          tcg.ac.customCurrencyFormat(total_promotion, decimal) +
          "</div>";
        var total_pnl = list[i].sports_game_pnl;
        var profitLossColor = total_pnl >= 0 ? "tbl-red" : "tbl-green";
        _html +=
          "<div class='divTableCell ps-num " +
          profitLossColor +
          "' data-switchDecimal='" +
          decimal +
          "' data-value='" +
          total_pnl +
          "'>" +
          tcg.ac.customCurrencyFormat(total_pnl, decimal) +
          "</div>";
        _html += "</div>";
      }
    } else {
      _html += "<div class='tableContent-wrp'>";
      _html += "<div class='noResult-data'>" + tcg.lib.prop("no_result") + "</div>";
      _html += "</div>";
    }
    $("#sportsAgentPnlList").html(_html);
  };
  tcg.ac.ui.switchPvpAgentPnlSummaryType = function () {
    $(document)
      .off("click", "#pvpAgentPnlForm .summaryTypeList li")
      .on("click", "#pvpAgentPnlForm .summaryTypeList li", function () {
        if (!$(this).hasClass("active") && !$("#pvpAgentPnlForm .form-submit").hasClass("processing")) {
          var rel = $(this).attr("data-rel");
          $("#pvpAgentPnlForm .summaryTypeList li").removeClass("active");
          $(this).addClass("active");
          $("#pvpAgentPnlForm [name='pageNo']").val(1);
          $("#pvpAgentPnlForm [name='summaryType']").val(rel);
          // $("#pvpAgentPnlForm .form-submit").click();
          tcg.ac.getPvpAgentPnl();
        }
      });
  };
  tcg.ac.ui.switchRNGAgentPnlSummaryType = function () {
    $(document)
      .off("click", "#rngAgentPnlForm .summaryTypeList li")
      .on("click", "#rngAgentPnlForm .summaryTypeList li", function () {
        if (!$(this).hasClass("active") && !$("#rngAgentPnlForm .form-submit").hasClass("processing")) {
          var rel = $(this).attr("data-rel");
          $("#rngAgentPnlForm .summaryTypeList li").removeClass("active");
          $(this).addClass("active");
          $("#rngAgentPnlForm [name='pageNo']").val(1);
          $("#rngAgentPnlForm [name='summaryType']").val(rel);
          $("#rngAgentPnlForm [name='customerName']").val("");
          // $("#rngAgentPnlForm .form-submit").click();
          tcg.ac.getRNGAgentPnl();
        }
      });
  };
  tcg.ac.ui.switchFishAgentPnlSummaryType = function () {
    $(document)
      .off("click", "#fishAgentPnlForm .summaryTypeList li")
      .on("click", "#fishAgentPnlForm .summaryTypeList li", function () {
        if (!$(this).hasClass("active") && !$("#rngAgentPnlForm .form-submit").hasClass("processing")) {
          var rel = $(this).attr("data-rel");
          $("#fishAgentPnlForm .summaryTypeList li").removeClass("active");
          $(this).addClass("active");
          $("#fishAgentPnlForm [name='pageNo']").val(1);
          $("#fishAgentPnlForm [name='summaryType']").val(rel);
          $("#fishAgentPnlForm [name='customerName']").val("");
          // $("#rngAgentPnlForm .form-submit").click();
          tcg.ac.getFishAgentPnl();
        }
      });
  };
  tcg.ac.ui.switchSportsAgentPnlSummaryType = function () {
    $(document)
      .off("click", "#sportsAgentPnlForm .summaryTypeList li")
      .on("click", "#sportsAgentPnlForm .summaryTypeList li", function () {
        if (!$(this).hasClass("active") && !$("#rngAgentPnlForm .form-submit").hasClass("processing")) {
          var rel = $(this).attr("data-rel");
          $("#sportsAgentPnlForm .summaryTypeList li").removeClass("active");
          $(this).addClass("active");
          $("#sportsAgentPnlForm [name='pageNo']").val(1);
          $("#sportsAgentPnlForm [name='summaryType']").val(rel);
          $("#sportsAgentPnlForm [name='customerName']").val("");
          // $("#rngAgentPnlForm .form-submit").click();
          tcg.ac.getSportsAgentPnl();
        }
      });
  };
  tcg.ac.ui.selectPvpAgentDownlinePnl = function () {
    $(document)
      .off("click", "#pvpAgentPnlList .tbl-link")
      .on("click", "#pvpAgentPnlList .tbl-link", function () {
        if (!$(this).hasClass("loading")) {
          var customerName = $(this).text(),
            customerId = $(this).attr("data-customerId"),
            isOpen = false;
          $("#pvpAgentPnlForm .breadcrumbs .b-list").each(function () {
            if ($(this).attr("data-customerId") == customerId) {
              isOpen = true;
            }
          });
          if (!isOpen) {
            $("#pvpAgentPnlForm .breadcrumbs .b-list").addClass("loading");
            $("#pvpAgentPnlList .tbl-link").addClass("loading");
            var _html =
              "<div class='arrow-small-con mem-icon2 inline-block b-list new-entry' data-customerId='" +
              customerId +
              "'>" +
              customerName +
              "</div>";
            $("#pvpAgentPnlForm .breadcrumbs").append(_html);
          }
          $("#pvpAgentPnlForm [name='pageNo']").val(1);
          tcg.ac.getPvpAgentPnl(customerId);
        }
      });
  };
  tcg.ac.ui.selectRNGAgentDownlinePnl = function () {
    $(document)
      .off("click", "#rngAgentPnlList .tbl-link")
      .on("click", "#rngAgentPnlList .tbl-link", function () {
        if (!$(this).hasClass("loading")) {
          var customerName = $(this).text(),
            customerId = $(this).attr("data-customerId"),
            isOpen = false;
          $("#rngAgentPnlForm .breadcrumbs .b-list").each(function () {
            if ($(this).attr("data-customerId") == customerId) {
              isOpen = true;
            }
          });
          if (!isOpen) {
            $("#rngAgentPnlForm .breadcrumbs .b-list").addClass("loading");
            $("#rngAgentPnlForm .tbl-link").addClass("loading");
            var _html =
              "<div class='arrow-small-con mem-icon2 inline-block b-list new-entry' data-customerId='" +
              customerId +
              "'>" +
              customerName +
              "</div>";
            $("#rngAgentPnlForm .breadcrumbs").append(_html);
          }
          $("#rngAgentPnlForm [name='pageNo']").val(1);
          //$("#rngAgentPnlForm [name='customerName']").val( customerName == window.sessionStorage.getItem("username") ? "" : customerName );
          tcg.ac.getRNGAgentPnl(customerId);
        }
      });
  };
  tcg.ac.ui.selectFishAgentDownlinePnl = function () {
    $(document)
      .off("click", "#fishAgentPnlList .tbl-link")
      .on("click", "#fishAgentPnlList .tbl-link", function () {
        if (!$(this).hasClass("loading")) {
          var customerName = $(this).text(),
            customerId = $(this).attr("data-customerId"),
            isOpen = false;
          $("#fishAgentPnlForm .breadcrumbs .b-list").each(function () {
            if ($(this).attr("data-customerId") == customerId) {
              isOpen = true;
            }
          });
          if (!isOpen) {
            $("#fishAgentPnlForm .breadcrumbs .b-list").addClass("loading");
            $("#fishAgentPnlForm .tbl-link").addClass("loading");
            var _html =
              "<div class='arrow-small-con mem-icon2 inline-block b-list new-entry' data-customerId='" +
              customerId +
              "'>" +
              customerName +
              "</div>";
            $("#fishAgentPnlForm .breadcrumbs").append(_html);
          }
          $("#fishAgentPnlForm [name='pageNo']").val(1);
          $("#fishAgentPnlForm [name='customerName']").val(
            customerName == window.sessionStorage.getItem("username") ? "" : customerName
          );
          tcg.ac.getFishAgentPnl();
        }
      });
  };
  tcg.ac.ui.selectSportsAgentDownlinePnl = function () {
    $(document)
      .off("click", "#sportsAgentPnlList .tbl-link")
      .on("click", "#sportsAgentPnlList .tbl-link", function () {
        if (!$(this).hasClass("loading")) {
          var customerName = $(this).text(),
            customerId = $(this).attr("data-customerId"),
            isOpen = false;
          $("#sportsAgentPnlForm .breadcrumbs .b-list").each(function () {
            if ($(this).attr("data-customerId") == customerId) {
              isOpen = true;
            }
          });
          if (!isOpen) {
            $("#sportsAgentPnlForm .breadcrumbs .b-list").addClass("loading");
            $("#sportsAgentPnlForm .tbl-link").addClass("loading");
            var _html =
              "<div class='arrow-small-con mem-icon2 inline-block b-list new-entry' data-customerId='" +
              customerId +
              "'>" +
              customerName +
              "</div>";
            $("#sportsAgentPnlForm .breadcrumbs").append(_html);
          }
          $("#sportsAgentPnlForm [name='pageNo']").val(1);
          $("#sportsAgentPnlForm [name='customerName']").val(
            customerName == window.sessionStorage.getItem("username") ? "" : customerName
          );
          tcg.ac.getSportsAgentPnl();
        }
      });
  };
  tcg.ac.ui.clickPvpAgentPnlBreadcrumbs = function () {
    $(document)
      .off("click", "#pvpAgentPnlForm .breadcrumbs .b-list")
      .on("click", "#pvpAgentPnlForm .breadcrumbs .b-list", function () {
        if (!$(this).hasClass("loading")) {
          var customerId = $(this).attr("data-customerId"),
            customerName = $(this).text(),
            removeFromList = false;
          $("#pvpAgentPnlForm .breadcrumbs .b-list").each(function () {
            if (removeFromList) $(this).remove();
            if ($(this).attr("data-customerId") == customerId) {
              removeFromList = true;
            }
          });
          $("#pvpAgentPnlForm [name='pageNo']").val(1);
          $("#pvpAgentPnlForm .breadcrumbs .b-list").addClass("loading");
          $("#pvpAgentPnlList .tbl-link").addClass("loading");
          tcg.ac.getPvpAgentPnl(customerId);
        }
      });
  };
  tcg.ac.ui.clickRNGAgentPnlBreadcrumbs = function () {
    $(document)
      .off("click", "#rngAgentPnlForm .breadcrumbs .b-list")
      .on("click", "#rngAgentPnlForm .breadcrumbs .b-list", function () {
        if (!$(this).hasClass("loading")) {
          var customerId = $(this).attr("data-customerId"),
            customerName = $(this).text(),
            removeFromList = false;
          $("#rngAgentPnlForm .breadcrumbs .b-list").each(function () {
            if (removeFromList) $(this).remove();
            if ($(this).attr("data-customerId") == customerId) {
              removeFromList = true;
            }
          });
          $("#rngAgentPnlForm [name='pageNo']").val(1);
          $("#rngAgentPnlForm .breadcrumbs .b-list").addClass("loading");
          $("#rngAgentPnlList .tbl-link").addClass("loading");
          //$("#rngAgentPnlForm [name='customerName']").val( customerName == window.sessionStorage.getItem("username") ? "" : customerName );
          tcg.ac.getRNGAgentPnl(customerId);
        }
      });
  };
  tcg.ac.ui.clickFishAgentPnlBreadcrumbs = function () {
    $(document)
      .off("click", "#fishAgentPnlForm .breadcrumbs .b-list")
      .on("click", "#fishAgentPnlForm .breadcrumbs .b-list", function () {
        if (!$(this).hasClass("loading")) {
          var customerId = $(this).attr("data-customerId"),
            customerName = $(this).text(),
            removeFromList = false;
          $("#fishAgentPnlForm .breadcrumbs .b-list").each(function () {
            if (removeFromList) $(this).remove();
            if ($(this).attr("data-customerId") == customerId) {
              removeFromList = true;
            }
          });
          $("#fishAgentPnlForm [name='pageNo']").val(1);
          $("#fishAgentPnlForm .breadcrumbs .b-list").addClass("loading");
          $("#fishAgentPnlForm .tbl-link").addClass("loading");
          $("#fishAgentPnlForm [name='customerName']").val(
            customerName == window.sessionStorage.getItem("username") ? "" : customerName
          );
          tcg.ac.getFishAgentPnl();
        }
      });
  };
  tcg.ac.ui.clickSportsAgentPnlBreadcrumbs = function () {
    $(document)
      .off("click", "#sportsAgentPnlForm .breadcrumbs .b-list")
      .on("click", "#sportsAgentPnlForm .breadcrumbs .b-list", function () {
        if (!$(this).hasClass("loading")) {
          var customerId = $(this).attr("data-customerId"),
            customerName = $(this).text(),
            removeFromList = false;
          $("#sportsAgentPnlForm .breadcrumbs .b-list").each(function () {
            if (removeFromList) $(this).remove();
            if ($(this).attr("data-customerId") == customerId) {
              removeFromList = true;
            }
          });
          $("#sportsAgentPnlForm [name='pageNo']").val(1);
          $("#sportsAgentPnlForm .breadcrumbs .b-list").addClass("loading");
          $("#sportsAgentPnlForm .tbl-link").addClass("loading");
          $("#sportsAgentPnlForm [name='customerName']").val(
            customerName == window.sessionStorage.getItem("username") ? "" : customerName
          );
          tcg.ac.getSportsAgentPnl();
        }
      });
  };

  // Agent Team Betting
  tcg.ac.ui.loadTeamBetting = function (result) {
    var decimal = 2,
      _html = "",
      _total = "",
      list = result.list,
      total = result.footer;
    if (list.length > 0) {
      for (var i = 0; i < list.length; i++) {
        var dateTime = list[i].create_time.split(" ");
        var prop = tcg.lib.prop(tcg.ac.getMerchant() + "_" + list[i].game_name);
        var gameName = prop == tcg.ac.getMerchant() + "_" + list[i].game_name ? list[i].game_name : prop;
        _html += "<div class='divTableRow' style='position: relative'>";
        _html += "<div class='divTableCell onel-th-x'>" + gameName + "</div>";
        _html +=
          "<div class='divTableCell onel-th-x mylink tbl-l'><span class='openItem' data-orderId='" +
          list[i].order_detail_id +
          "' data-chasing='" +
          list[i].chase +
          "' data-orderMasterId='" +
          list[i].order_master_id +
          "'>" +
          tcg.ac.shortenText(list[i].order_num) +
          "</span></div>"; //  yel-pop
        _html += "<div class='divTableCell onel-th-x'>" + list[i].customer_name + "</div>"; // yel-pop
        _html +=
          "<div class='divTableCell onel-th-x div-long'>" +
          dateTime[0] +
          " <span class='tclDec'>" +
          dateTime[1] +
          "</span></div>";
        _html += "<div class='divTableCell onel-th-x div-long2'>" + list[i].numero + "</div>";
        /*_html += "<div class='divTableCell onel-th-x div-y'>" +tcg.lib.prop("bettingMode_"+list[i].bet_mode)+ "</div>";*/
        _html += "<div class='divTableCell onel-th-x div-y'>" + list[i].chase_remark + "</div>";
        _html +=
          "<div class='divTableCell onel-th-x ps-num2' data-switchDecimal='" +
          decimal +
          "' data-value='" +
          list[i].plan_bet_amount +
          "'>" +
          tcg.ac.customCurrencyFormat(list[i].plan_bet_amount, decimal) +
          "</div>";
        _html +=
          "<div class='divTableCell onel-th-x ps-num2' data-switchDecimal='" +
          decimal +
          "' data-value='" +
          list[i].actual_bet_amount +
          "'>" +
          tcg.ac.customCurrencyFormat(list[i].actual_bet_amount, decimal) +
          "</div>";
        var color = list[i].status == 4 ? "tbl-red" : "";
        _html +=
          "<div class='divTableCell onel-th-x ps-num2 " +
          color +
          "' data-switchDecimal='" +
          decimal +
          "' data-value='" +
          list[i].win_amount +
          "'>" +
          tcg.ac.customCurrencyFormat(list[i].win_amount, decimal) +
          "</div>";
        _html +=
          "<div class='divTableCell onel-th-x div-z'>" + tcg.lib.prop("orderStatus_" + list[i].status) + "</div>";
        _html += "</div>";
      }
    } else {
      _html += "<div class='tableContent-wrp'>";
      _html += "<div class='noResult-data'>" + tcg.lib.prop("no_result") + "</div>";
      _html += "</div>";
    }

    $("#teamBettingList").html(_html);
    var totalBetAmt = result.list.length == 0 ? 0 : total.total_plan_bet_amount;
    var totalActualBetAmt = result.list.length == 0 ? 0 : total.total_actual_bet_amount;
    var totalWinAmt = result.list.length == 0 ? 0 : total.total_win_amount;
    _total += "<div class='tbl-total-con inline-block'>" + tcg.lib.prop("ac_planned_bet_amount_total") + "</div>";
    _total +=
      "<div class='tbl-total-amt inline-block' data-switchDecimal='" +
      decimal +
      "' data-value='" +
      totalBetAmt +
      "'>" +
      tcg.ac.customCurrencyFormat(totalBetAmt, decimal) +
      "</div>";
    _total += "<div class='tbl-total-con inline-block'>" + tcg.lib.prop("ac_total_valid_betting_amount") + "</div>";
    _total +=
      "<div class='tbl-total-amt inline-block' data-switchDecimal='" +
      decimal +
      "' data-value='" +
      totalActualBetAmt +
      "'>" +
      tcg.ac.customCurrencyFormat(totalActualBetAmt, decimal) +
      "</div>";
    _total += "<div class='tbl-total-con inline-block'>" + tcg.lib.prop("ac_total_amount_prize_money") + "</div>";
    _total +=
      "<div class='tbl-total-amt inline-block' data-switchDecimal='" +
      decimal +
      "' data-value='" +
      totalWinAmt +
      "'>" +
      tcg.ac.customCurrencyFormat(totalWinAmt, decimal) +
      "</div>";
    $("#totalTeamBetting").html(_total);

    $("#lottoTeamBetting input[name='order']").on("change", function () {
      $(this).val($(this).val().toUpperCase());
    });
  };
  // tcg.ac.ui.loadLottoGameHistoryItem = function (result, orderMasterId, chasing) {
  //     var decimal = 2;
  //     globalVar.orderMasterId = orderMasterId;
  //     globalVar.orderMultiple = result.multiple;
  //     $("#itemWrapper .prsnl-game-name").html("<span id='rm_" + result.gameCode + "'>&nbsp;</span>");
  //     var orderNumber = tcg.ac.shortenText(result.orderNumber);
  //     if (!$("#chasing").hasClass("hide")) {
  //         $("#chasing").addClass("hide");
  //     }
  //     ;
  //     if (chasing == true || chasing == "true") {
  //         var chasingOrder = result.chasingOrder;
  //         orderNumber = orderNumber + "-" + tcg.ac.ui.strPad(chasingOrder, 3);
  //         $("#chasing").removeClass("hide");
  //     }
  //     $("#itemWrapper #lotteryNum").removeClass("double");
  //     $("#itemWrapper .orderNo").text(orderNumber);
  //     $("#itemWrapper .seriesModel").text(result.series + "/" + tcg.lib.prop("bettingMode_" + result.bettingModeCode));
  //     $("#itemWrapper .prizeMoney").html(result.winningAmount == null ? "-" : result.winningAmount.toFixed(4));
  //     $("#itemWrapper .bettingNoteCount").text(result.stakes);
  //     $("#itemWrapper .bettingMultiples").html(tcg.ac.currencyFormat(result.multiple));
  //     $("#itemWrapper .bettingTime").text(tcg.ac.timeToDateFormat(result.bettingTime, "dateTime"));
  //     $("#itemWrapper .orderStatus").text(tcg.lib.prop("orderStatus_" + result.orderStatus));
  //     $("#itemWrapper .bettingOnNumber").text(result.numero);
  //     var lotteryNumbers = result.winningNumber == null ? "-" : result.winningNumber;
  //     var splitChar = lotteryNumbers.indexOf(",") < 0 ? "" : ",";
  //     $("#itemWrapper #lotteryNum").html("<span>" + lotteryNumbers.split(splitChar).join("</span><span>") + "</span>");
  //     if (result.winningNumber != null) {
  //         $("#itemWrapper #lotteryNum").addClass("lotteryNumbers")
  //     }
  //     ;
  //     if (splitChar == ",") {
  //         $("#itemWrapper #lotteryNum").addClass("double")
  //     }
  //     ;
  //     var _html = "", pageNo = $("#itemWrapper [name='pageNo']");
  //     var data = {
  //         page: (pageNo.val() * 1) - 1,
  //         size: 100
  //     }
  //     tcg.ac.api.orderInfos(data, function (orderInfo, textStatus, jqXHR) {
  //         var orderInfos = orderInfo.content;
  //         for (var i = 0; i < orderInfos.length; i++) {
  //             var betRow = tcg.ac.betMap(orderInfos[i]);
  //             _html += "<div class='divTableRow'>";
  //             _html += "<div class='divTableCell onel-cl-y ps-cen yel-pop'>" + lottTranslator.translatePlayName(orderInfos[i].playId,orderInfos[i].playCode,orderInfos[i].bettingContent);
  //             _html += "<div class='pop-yellow' style='left:0'>" + lottTranslator.translatePlayName(orderInfos[i].playId,orderInfos[i].playCode,orderInfos[i].bettingContent) + "</div>";
  //             _html += "</div>"
  //             _html += "<div class='divTableCell onel-cl-y ps-cen yel-pop xdaLs'>" + lottTranslator.translateBallContentText(orderInfos[i].playId,orderInfos[i].bettingContent) + "<div class='pop-yellow'>";
  //             _html += lottTranslator.translateBallContentText(orderInfos[i].playId,orderInfos[i].bettingContent);
  //             _html += "</div></div>";
  //             _html += "<div class='divTableCell onel-cl-y ps-cen xd2-xda'>" + orderInfos[i].stakes + "</div>";
  //             var multiple = (orderInfos[i].multiple * 1) * (globalVar.orderMultiple * 1);
  //             _html += "<div class='divTableCell onel-cl-y ps-cen xd3-xda'>" + orderInfos[i].series + "/" + tcg.lib.prop("bettingMode_" + orderInfos[i].bettingMode) + "/" + multiple + "</div>";
  //             var total_bet_amt = (orderInfos[i].bettingAmount * 1) * (globalVar.orderMultiple * 1);
  //             _html += "<div class='divTableCell onel-cl-y ps-cen xg-xlass' data-switchDecimal='" + decimal + "' data-value='" + total_bet_amt + "'>" + tcg.ac.customCurrencyFormat(total_bet_amt, decimal) + "</div>";
  //             _html += "</div>";
  //         }

  //         if (result.orderStatus == 2) {
  //             $("#cancelGameHistoryDetail").parent().show();
  //         } else {
  //             $("#cancelGameHistoryDetail").parent().hide();
  //         }
  //         $("#itemList").html(_html);
  //         $(".moreText").on("click", function () {
  //             tcg.ac.showLongText($(this).attr("data-text"));
  //         });
  //         tcg.ac.ui.loadPagination("itemWrapper", pageNo.val(), orderInfo.totalPages, "#orderDetailPagination");
  //     });
  //     tcg.ac.switchDecimal("#switchDecimal");
  //     $("#chasing").on("click", function () {
  //         sessionStorage.setItem("orderMasterId", orderMasterId);
  //         sessionStorage.setItem("chasingOrder", result.chasingOrder);
  //         sessionStorage.setItem("chasing", chasing);
  //         $('.model_child_menus li[data-submenu="norecordChase"]').trigger('click');
  //     })
  //     tcg.ac.ui.goBackToGameHistory();
  //     tcg.ac.ui.addCopySelection('js-copybtn');
  // };
  tcg.ac.ui.loadOrderDetail = function (result, icon) {
    globalVar.orderMasterId = result.orderMasterId;
    globalVar.orderMultiple = result.multiple;

    $("#gameHistoryDetail .prsnl-game-name").html(
      "<span id='rm_" +
        result.gameCode +
        "' style='background-image: url(" +
        icon[result.gameCode] +
        ");background-size: 100%;background-repeat: no-repeat;background-position: center;'>&nbsp;</span>"
    );

    var orderNumber = tcg.ac.shortenText(result.orderNumber);
    if (!$("#chasing").hasClass("hide")) {
      $("#chasing").addClass("hide");
    }
    var chasing = result.chasingOrder;
    if (chasing == true || chasing == "true") {
      var chasingOrder = result.chasingOrder;
      orderNumber = orderNumber + "-" + tcg.ac.ui.strPad(chasingOrder, 3);
      $("#chasing").removeClass("hide");
    }
    $("#gameHistoryDetail #lotteryNum").removeClass("double");
    $("#gameHistoryDetail .orderNo").text(orderNumber);
    $("#gameHistoryDetail .bettingTime").text(tcg.ac.timeToDateFormat(result.bettingTime, "dateTime"));
    $("#gameHistoryDetail .prizeMoney").html(result.winningAmount == null ? "-" : result.winningAmount.toFixed(4));
    $("#gameHistoryDetail .bettingNoteCount").text(result.stakes);
    $("#gameHistoryDetail .orderStatus").text(tcg.lib.prop("orderStatus_" + result.orderStatus));
    var lotteryNumbers = result.winningNumber == null ? "-" : result.winningNumber;
    var splitChar = lotteryNumbers.indexOf(",") < 0 ? "" : ",";
    $("#gameHistoryDetail #lotteryNum").html(
      "<span>" + lotteryNumbers.split(splitChar).join("</span><span>") + "</span>"
    );
    $("#gameHistoryDetail .bettingOnNumber").text(result.numero);
  };
  tcg.ac.ui.loadOrderInfo = function (result) {
    var decimal = 2;
    var html = "";
    var content = result.content;
    $("#member_management #gameHistoryDetail #itemList").html("");
    for (var i = 0; i < content.length; i++) {
      html += '<div class="divTableRow">';
      html +=
        '<div class="divTableCell onel-cl-y ps-cen yel-npop yel-pop">' +
        lottTranslator.translatePlayName(content[i].playId, content[i].playCode, content[i].bettingContent) +
        '<div class="pop-yellow" style="left:0">' +
        lottTranslator.translatePlayName(content[i].playId, content[i].playCode, content[i].bettingContent) +
        "</div></div>";
      html +=
        '<div class="divTableCell onel-cl-y ps-cen yel-pop xdaLs"><span class="divTableCellnspan">' +
        lottTranslator.translateBallContentText(content[i].playId, content[i].bettingContent) +
        '</span><div class="pop-yellow">' +
        lottTranslator.translateBallContentText(content[i].playId, content[i].bettingContent) +
        "</div></div>";
      html += '<div class="divTableCell onel-cl-y ps-cen xg-xlass">' + content[i].stakes + "</div>";
      html +=
        '<div class="divTableCell onel-cl-y ps-cen xd3-xda">' +
        content[i].series +
        "/元/" +
        content[i].multiple +
        "</div>";
      var multiple = content[i].multiple * 1 * (globalVar.orderMultiple * 1);
      var total_bet_amt = content[i].bettingAmount * 1 * (globalVar.orderMultiple * 1);
      html +=
        "<div class='divTableCell onel-cl-y ps-cen xg-xlass' data-switchDecimal='" +
        decimal +
        "' data-value='" +
        total_bet_amt +
        "'>" +
        tcg.ac.customCurrencyFormat(total_bet_amt, decimal) +
        "</div>";
      html += "</div>";
    }
    return html;
  };
  tcg.ac.ui.goBackToGameHistory = function () {
    $(document)
      .off("click", "#backToGameHistory")
      .on("click", "#backToGameHistory", function () {
        var lastPage = sessionStorage.getItem("lastPage"),
          childMenu = sessionStorage.getItem("childMenu");
        switch (lastPage) {
          case "teamBetting":
            $("#itemWrapper").addClass("hide");
            $("#teamBetting").removeClass("hide");
            tcg.ac.searchTeamBetting();
            break;
          default:
        }
        sessionStorage.removeItem("lastPage");
      });
  };
  /*when click copy the text*/
  tcg.ac.ui.addCopySelection = function (selectionId) {
    // remove style
    document.getElementById(selectionId).removeAttribute("style", true);

    $(document)
      .off("click", "#" + selectionId)
      .on("click", "#" + selectionId, function () {
        var text = $(this).html();
        try {
          // set background highlight
          $(this).css({
            "background-color": "#004A88",
            color: "#fff",
          });

          // for IE
          if (window.clipboardData && window.clipboardData.setData) {
            window.clipboardData.setData("Text", text);
          } else {
            document.execCommand("copy");
            document.execCommand("copy", false, text);
          }
        } catch (err) {
          alert("Oops, unable to copy");
        }
      });
  };
  tcg.ac.ui.switchAgentTeamBetting = function () {
    $(document)
      .off("click", "#switchAgentTeamBetting li")
      .on("click", "#switchAgentTeamBetting li", function () {
        if ($(this).attr("data-rel") == "lotto") {
          $("#pvpTeamBetting").addClass("hide");
          $("#lottoTeamBetting").removeClass("hide");
          $("#lottoTeamBettingForm .form-submit").click();
        } else {
          $("#lottoTeamBetting").addClass("hide");
          $("#pvpTeamBetting").removeClass("hide");
          $("#pvpTeamBettingForm .form-submit").click();
        }
        $("#switchAgentTeamBetting li.active").removeClass("active");
        $(this).addClass("active");
      });
  };
  tcg.ac.ui.loadGroupGames = function (_html) {
    $("#customGameList").html(_html);
    $("#lottoTeamBettingForm .form-submit").click();
    tcg.ac.ui.customSelectGame("#lottoTeamBettingForm [name='game']");
  };
  tcg.ac.ui.customSelectGame = function (id) {
    var id = id;
    $(document)
      .off("click", id)
      .on("click", id, function () {
        var parentDiv = $(this).parents(".selectGame");
        var input = parentDiv.find("[name='game']");
        parentDiv.addClass("show-data");
        parentDiv
          .find(".gameList .game")
          .unbind("click")
          .bind("click", function (e) {
            var selected = $(this),
              gameCode = selected.attr("data-gameCode");
            if (gameCode == "") {
              input.val(selected.text()).attr({ "data-gameCode": selected.attr("data-gameCode") });
              parentDiv.find(".gameList .game").removeClass("selected");
              parentDiv.find(".gameList .game-all").addClass("selected");
            } else {
              parentDiv.find(".gameList .game-all").removeClass("selected");
              var arrVal = input.attr("data-gameCode") == "" ? [] : input.val().split(","),
                arrCode = input.attr("data-gameCode") == "" ? [] : input.attr("data-gameCode").split(",");
              if ($(this).hasClass("selected")) {
                var index = arrVal.indexOf(selected.text());
                if (index >= 0) {
                  arrVal.splice(index, 1);
                }
                var index = arrCode.indexOf(gameCode);
                if (index >= 0) {
                  arrCode.splice(index, 1);
                }
                selected.removeClass("selected");
              } else {
                if (arrCode.indexOf(gameCode) < 0) {
                  arrVal.push(selected.text());
                  arrCode.push(gameCode);
                  selected.addClass("selected");
                }
              }
              if (arrVal.length > 0) {
                input.val(arrVal.join(","));
                input.attr({ "data-gameCode": arrCode.join(",") });
              } else {
                input.val("全部");
                input.attr({ "data-gameCode": "" });
                parentDiv.find(".gameList .game").removeClass("selected");
                parentDiv.find(".gameList .game-all").addClass("selected");
              }
            }
            // $(document).off("click","html");
            // parentDiv.removeClass("show-data");
          });
        $(document)
          .off("click", "html")
          .on("click", "html", function (e) {
            if (e.target.id == "customGameList" || $(e.target).parents("#customGameList")[0]) {
            } else {
              $(document).off("click", "html");
              parentDiv.removeClass("show-data");
            }
          });
      });
  };
  tcg.ac.ui.loadGameRooms = function (targetId, gameType, type) {
    var rng = "",
      pvp = "";

    rng += "<option value='game_ag_6'>" + tcg.lib.prop("ac_subordinate_management_betting_history_tab_4") + "</option>";
    pvp += "<option value=''>" + tcg.lib.prop("ac_all") + "</option>";
    pvp += "<option value='bjl_room'>" + tcg.lib.prop("ac_game_baccarat") + "</option>";
    pvp += "<option value='gswz_room'>" + tcg.lib.prop("ac_game_5_in_hk") + "</option>";
    pvp += "<option value='thirteen_room'>" + tcg.lib.prop("ac_game_13_water") + "</option>";
    pvp += "<option value='tbnn_room'>" + tcg.lib.prop("ac_game_tiberius") + "</option>";
    pvp += "<option value='nn_room'>" + tcg.lib.prop("ac_game_taurus") + "</option>";
    pvp += "<option value='zjh_room'>" + tcg.lib.prop("ac_game_zhunjinhua") + "</option>";
    $(targetId).html(gameType == 0 || gameType == "PVP" ? pvp : rng);

    var type = type || "set";
    if (type == "set") {
      tcg.ac.ui.customSelect(targetId);
    } else {
      $(targetId).trigger("chosen:updated");
    }
  };
  tcg.ac.ui.showMorePvpFilter = function () {
    $(document)
      .off("click", "#pvpTeamBettingForm .tabBtn")
      .on("click", "#pvpTeamBettingForm .tabBtn", function () {
        var form = $("#pvpTeamBettingForm"),
          otherFilter = form.find(".otherFilter"),
          table = $("#tableContainer");
        if (otherFilter.hasClass("hide")) {
          $(this).addClass("active");
          otherFilter.removeClass("hide");
          table.addClass("mini");
          $("#lottoGameHistoryList").addClass("y-overflow");
          tcg.ac.ui.customSelect("#lottoGameHistoryForm [name='chaseStatus']");
        } else {
          $(this).removeClass("active");
          otherFilter.addClass("hide");
          table.removeClass("mini");
          $("#lottoGameHistoryList").removeClass("y-overflow");
        }
      });
  };
  tcg.ac.ui.searchPvpTeamBetting = function () {
    $(document)
      .off("click", "#pvpTeamBettingForm .form-submit")
      .on("click", "#pvpTeamBettingForm .form-submit", function () {
        tcg.ac.getPvpTeamBetting();
      });
  };
  tcg.ac.ui.loadPvpTeamBetting = function (result, gameType) {
    var decimal = 2,
      _html = "",
      _total = "",
      total = result.footer,
      list = result.list;

    if (list.length > 0) {
      for (var i = 0; i < list.length; i++) {
        _html += "<div class='divTableRow'>";
        var gameType =
          list[i].game_category == "PVP"
            ? tcg.lib.prop("ac_subordinate_management_betting_history_tab_6")
            : tcg.lib.prop("ac_subordinate_management_betting_history_tab_4");
        _html += "<div class='divTableCell'>" + gameType + "</div>";
        _html += "<div class='divTableCell'>" + list[i].game_name + "</div>";
        _html += "<div class='divTableCell'>" + list[i].customer_name + "</div>";
        _html +=
          "<div class='divTableCell' data-switchDecimal='" +
          decimal +
          "' data-value='" +
          list[i].game_loses +
          "'>" +
          tcg.ac.customCurrencyFormat(list[i].game_loses, decimal) +
          "</div>";
        _html +=
          "<div class='divTableCell' data-switchDecimal='" +
          decimal +
          "' data-value='" +
          list[i].game_winnings +
          "'>" +
          tcg.ac.customCurrencyFormat(list[i].game_winnings, decimal) +
          "</div>";
        var netProfitColor = list[i].net_profit >= 0 ? "tbl-red" : "tbl-green";
        _html +=
          "<div class='divTableCell " +
          netProfitColor +
          "' data-switchDecimal='" +
          decimal +
          "' data-value='" +
          list[i].net_profit +
          "'>" +
          tcg.ac.customCurrencyFormat(list[i].net_profit, decimal) +
          "</div>";
        _html += "</div>";
      }
    } else {
      _html += "<div class='tableContent-wrp'>";
      _html += "<div class='noResult-data'>" + tcg.lib.prop("no_result") + "</div>";
      _html += "</div>";
    }

    $("#pvpTeamBettingTable .betLost").text(
      gameType == "PVP" ? tcg.lib.prop("ac_net_money") : tcg.lib.prop("ac_bet_amount")
    );
    $("#pvpTeamBettingTable .prizeWon").text(
      gameType == "PVP" ? tcg.lib.prop("ac_net_amount_of_money") : tcg.lib.prop("ac_winning_amount")
    );

    $("#pvpTeamBettingList").html(_html);
    var _total = "",
      total = result.footer,
      label_betLoss = gameType == "PVP" ? tcg.lib.prop("ac_net_amount_money") : tcg.lib.prop("ac_bet_amount_total"),
      label_betWin =
        gameType == "PVP" ? tcg.lib.prop("ac_total_amount_net_win") : tcg.lib.prop("ac_total_amount_prize"),
      label_pnl =
        gameType == "PVP"
          ? tcg.lib.prop("ac_game_profit_and_loss_total")
          : tcg.lib.prop("ac_game_profit_and_loss_total"),
      totalGameLoses = list.length == 0 ? 0 : total.game_loses,
      totalGameWinnings = list.length == 0 ? 0 : total.game_winnings,
      totalNetProfit = list.length == 0 ? 0 : total.net_profit;
    _total += "<div class='tbl-total-con inline-block'>" + label_betLoss + ":</div>";
    _total +=
      "<div class='tbl-total-amt inline-block' data-switchdecimal='" +
      decimal +
      "' data-value='" +
      totalGameLoses +
      "'>" +
      tcg.ac.customCurrencyFormat(totalGameLoses, decimal) +
      "</div>";
    _total += "<div class='tbl-total-con inline-block'>" + label_betWin + ":</div>";
    _total +=
      "<div class='tbl-total-amt inline-block' data-switchdecimal='" +
      decimal +
      "' data-value='" +
      totalGameWinnings +
      "'>" +
      tcg.ac.customCurrencyFormat(totalGameWinnings, decimal) +
      "</div>";
    _total += "<div class='tbl-total-con inline-block'>" + label_pnl + ":</div>";
    _total +=
      "<div class='tbl-total-amt inline-block' data-switchdecimal='" +
      decimal +
      "' data-value='" +
      totalNetProfit +
      "'>" +
      tcg.ac.customCurrencyFormat(totalNetProfit, decimal) +
      "</div>";
    $("#pvpTeamBettingTotal").html(_total);
  };

  // Personal History
  tcg.ac.ui.loadRngReportsWalletCopy = function (vendors) {
    var _html = "",
      _num = 0;
    for (var i = 0; i < vendors.length; i++) {
      if (vendors[i] == "SAFE_BOX" || vendors[i] == "LOTT") continue;
      var cn = _num == 0 ? "default" : "";
      _html += "<li data-value='" + vendors[i] + "' class='" + cn + "'>" + vendors[i] + "</li>";
      _num++;
    }
    if (_num == 0) {
      return false;
    }
    return _html;
  };
  tcg.ac.ui.loadRngReportsCopy = function (data) {
    var _html = "",
      decimal = 2;
    var subTotalBetAmt = 0;
    var subTotalWinAmt = 0;
    var list = data.list;
    var footer = data.footer;
    if (list.length > 0) {
      for (var i = 0; i < list.length; i++) {
        var color = i % 2 == 1 ? "tbl-tr-even" : "tbl-tr-odd";
        _html += '<div class="divTableRow ' + color + '">';
        _html += '<div class="divTableCell">' + list[i].customerName + "</div>";
        _html += '<div class="divTableCell long">' + moment(list[i].betTime).format("YYYY-MM-DD HH:mm:ss") + "</div>";
        _html +=
          '<div class="divTableCell" data-switchDecimal="' +
          decimal +
          '" data-value="' +
          list[i].betAmount +
          '">' +
          tcg.ac.currencyFormat(list[i].betAmount, decimal) +
          "</div>";
        _html +=
          '<div class="divTableCell" data-switchDecimal="' +
          decimal +
          '" data-value="' +
          list[i].validBetAmount +
          '">' +
          tcg.ac.currencyFormat(list[i].validBetAmount, decimal) +
          "</div>";
        _html +=
          '<div class="divTableCell" data-switchDecimal="' +
          decimal +
          '" data-value="' +
          list[i].winAmount +
          '">' +
          tcg.ac.currencyFormat(list[i].winAmount, decimal) +
          "</div>";
        _html +=
          '<div class="divTableCell" data-switchDecimal="' +
          decimal +
          '" data-value="' +
          list[i].profitAndLoss +
          '">' +
          tcg.ac.currencyFormat(list[i].profitAndLoss, decimal) +
          "</div>";
        _html += '<div class="divTableCell" data-value="' + list[i].gameNameCN + '">' + list[i].gameNameCN + "</div>";
        _html += '<div class="divTableCell">' + list[i].orderNo + "</div>";
        _html += "</div>";
      }
      // SubTotal
      $("#betHistoryTotal .betAmount").text(tcg.ac.currencyFormat(footer.betAmount, decimal)).attr({
        "data-switchDecimal": decimal,
        "data-value": footer.betAmount,
      });
      $("#betHistoryTotal .subTotalBetAmt").text(tcg.ac.currencyFormat(footer.validBetAmount, decimal)).attr({
        "data-switchDecimal": decimal,
        "data-value": footer.validBetAmount,
      });
      $("#betHistoryTotal .subTotalWinAmt").text(tcg.ac.currencyFormat(footer.winAmount, decimal)).attr({
        "data-switchDecimal": decimal,
        "data-value": footer.winAmount,
      });
      $("#betHistoryTotal .subTotalPnl").text(tcg.ac.currencyFormat(footer.profitAndLoss, decimal)).attr({
        "data-switchDecimal": decimal,
        "data-value": footer.profitAndLoss,
      });
    } else {
      _html += "<div class='divTableRow dtr_noRecord'>";
      _html += "<div class='divTableCell ps-strings'>" + tcg.lib.prop("ac_reports_no_data") + "</div>";
      _html += "</div>";
    }
    return _html;
  };
  tcg.ac.ui.loadPvpReportsCopy = function (data) {
    var _html = "",
      decimal = 2;
    var subTotalBetAmt = 0;
    var subTotalWinAmt = 0;
    var list = data.list;
    var footer = data.footer;
    if (list.length > 0) {
      for (var i = 0; i < list.length; i++) {
        var color = i % 2 == 1 ? "tbl-tr-even" : "tbl-tr-odd";
        _html += '<div class="divTableRow ' + color + '">';
        _html += '<div class="divTableCell ps-strings">' + list[i].customerName + "</div>";
        _html +=
          '<div class="divTableCell ps-strings">' + moment(list[i].betTime).format("YYYY-MM-DD HH:mm:ss") + "</div>";
        _html +=
          '<div class="divTableCell ps-strings" data-switchDecimal="' +
          decimal +
          '" data-value="' +
          list[i].startMoney +
          '">' +
          tcg.ac.currencyFormat(list[i].startMoney, decimal) +
          "</div>";
        _html +=
          '<div class="divTableCell ps-strings" data-switchDecimal="' +
          decimal +
          '" data-value="' +
          list[i].endMoney +
          '">' +
          tcg.ac.currencyFormat(list[i].endMoney, decimal) +
          "</div>";
        _html +=
          '<div class="divTableCell ps-strings" data-switchDecimal="' +
          decimal +
          '" data-value="' +
          list[i].profitAndLoss +
          '">' +
          tcg.ac.currencyFormat(list[i].profitAndLoss, decimal) +
          "</div>";
        _html +=
          '<div class="divTableCell ps-strings" data-value="' +
          list[i].gameNameCN +
          '">' +
          list[i].gameNameCN +
          "</div>";
        _html += '<div class="divTableCell ps-strings">' + list[i].orderNo + "</div>";
        _html += "</div>";
      }
      // SubTotal
      // $("#betHistoryTotal .subTotalBetAmt").text( tcg.ac.currencyFormat(footer.validBetAmount, decimal) ).attr({"data-switchDecimal": decimal, "data-value": footer.validBetAmount });
      // $("#betHistoryTotal .subTotalWinAmt").text( tcg.ac.currencyFormat(footer.winAmount, decimal) ).attr({"data-switchDecimal": decimal, "data-value": footer.winAmount });
      $("#betHistoryTotal .subTotalPnl").text(tcg.ac.currencyFormat(footer.profitAndLoss, decimal)).attr({
        "data-switchDecimal": decimal,
        "data-value": footer.profitAndLoss,
      });
    } else {
      _html += "<div class='divTableRow dtr_noRecord'>";
      _html += "<div class='divTableCell ps-strings'>" + tcg.lib.prop("ac_reports_no_data") + "</div>";
      _html += "</div>";
    }
    return _html;
  };
  tcg.ac.ui.loadSportsReportsCopy = function (api, data) {
    var _html = "",
      decimal = 2;
    var subTotalBetAmt = 0;
    var subTotalWinAmt = 0;
    var list = data.list;
    var footer = data.footer;
    if (list.length > 0) {
      for (var i = 0; i < list.length; i++) {
        var color = i % 2 == 1 ? "tbl-tr-even" : "tbl-tr-odd";
        _html += '<div class="divTableRow ' + color + '">';
        _html += '<div class="divTableCell ps-strings">' + list[i].customerName + "</div>";
        _html +=
          '    <div class="divTableCell ps-strings dateCell">' +
          moment(orders[i].betTime).format("YYYY-MM-DD HH:mm:ss") +
          "</div>";
        _html +=
          '    <div class="divTableCell ps-strings dateCell">' +
          moment(orders[i].transTime).format("YYYY-MM-DD HH:mm:ss") +
          "</div>";
        _html +=
          '    <div class="divTableCell ps-strings ac_tooltipster_hover longName" data-value="' +
          list[i].selection +
          '"><span class="ac_elipser">' +
          tcg.ac.strSub(list[i].selection, 15) +
          '</span><span class="ac_tooltipster hide">' +
          list[i].selection +
          "</span></div>";
        _html +=
          '    <div class="divTableCell ps-strings" data-switchDecimal="' +
          decimal +
          '" data-value="' +
          list[i].odds +
          '">' +
          tcg.ac.currencyFormat(list[i].odds, decimal) +
          "</div>";
        _html +=
          '    <div class="divTableCell ps-strings" data-switchDecimal="' +
          decimal +
          '" data-value="' +
          list[i].betAmount +
          '">' +
          tcg.ac.currencyFormat(list[i].betAmount, decimal) +
          "</div>";
        var winAmt = list[i].winAmount ? list[i].winAmount : 0;
        var rowStatus = list[i].status ? tcg.lib.prop("status_order_" + list[i].status) : "-";
        _html +=
          '    <div class="divTableCell ps-strings" data-switchDecimal="' +
          decimal +
          '" data-value="' +
          list[i].winAmt +
          '">' +
          tcg.ac.currencyFormat(winAmt, decimal) +
          "</div>";
        _html +=
          '    <div class="divTableCell ps-strings betStatus"  data-value="' + rowStatus + '" >' + rowStatus + "</div>";
        _html +=
          '    <div class="divTableCell ps-strings ac_tooltipster_hover longName" data-value="' +
          list[i].eventName +
          '"><span class="ac_elipser">' +
          tcg.ac.strSub(list[i].eventName, 15) +
          '</span><span class="ac_tooltipster hide">' +
          list[i].eventName +
          "</span></div>";
        _html += "</div>";
      }
      // SubTotal
      if (api == "set") {
        $("#betHistoryTotal .subTotalPnl").text(tcg.ac.currencyFormat(footer.profitAndLoss, decimal)).attr({
          "data-switchDecimal": decimal,
          "data-value": footer.profitAndLoss,
        });
        $("#betHistoryTotal .subTotalBetAmt").text(tcg.ac.currencyFormat(footer.validBetAmount, decimal)).attr({
          "data-switchDecimal": decimal,
          "data-value": footer.validBetAmount,
        });
        $("#betHistoryTotal .subTotalWinAmt").text(tcg.ac.currencyFormat(footer.winAmount, decimal)).attr({
          "data-switchDecimal": decimal,
          "data-value": footer.winAmount,
        });
      } else {
        $("#betHistoryTotal .subTotalPnl").text(tcg.ac.currencyFormat(footer.profitAndLoss, decimal)).attr({
          "data-switchDecimal": decimal,
          "data-value": footer.profitAndLoss,
        });
        footer.winAmount = footer.winAmount ? tcg.mc.currencyFormat(footer.winAmount, decimal) : 0;
        $("#betHistoryTotal .subTotalBetAmt").text(tcg.ac.currencyFormat(footer.betAmount, decimal)).attr({
          "data-switchDecimal": decimal,
          "data-value": footer.betAmount,
        });
        $("#betHistoryTotal .subTotalWinAmt").text(tcg.ac.currencyFormat(footer.winAmount, decimal)).attr({
          "data-switchDecimal": decimal,
          "data-value": footer.winAmount,
        });
      }
    } else {
      _html += "<div class='divTableRow dtr_noRecord'>";
      _html += "<div class='divTableCell ps-strings'>" + tcg.lib.prop("ac_reports_no_data") + "</div>";
      _html += "</div>";
    }
    return _html;
  };
  tcg.ac.ui.loadElottReportsCopy = function (api, data) {
    var _html = "",
      decimal = 2;
    var subTotalBetAmt = 0;
    var subTotalWinAmt = 0;
    var list = data.list;
    var footer = data.footer;
    if (list.length > 0) {
      for (var i = 0; i < list.length; i++) {
        var color = i % 2 == 1 ? "tbl-tr-even" : "tbl-tr-odd";
        _html += '<div class="divTableRow ' + color + '">';
        _html += '<div class="divTableCell ps-strings">' + list[i].customerName + "</div>";
        _html += '    <div class="divTableCell ps-strings">' + list[i].gameNameCN + "</div>";
        _html +=
          '    <div class="divTableCell ps-strings">' +
          moment(list[i].betTime).format("YYYY-MM-DD HH:mm:ss") +
          "</div>";
        _html +=
          '    <div class="divTableCell ps-strings" data-switchDecimal="' +
          decimal +
          '" data-value="' +
          list[i].betAmount +
          '">' +
          tcg.ac.currencyFormat(list[i].betAmount, decimal) +
          "</div>";
        var validBetAmount = list[i].validBetAmount ? list[i].validBetAmount : 0;
        _html +=
          '    <div class="divTableCell ps-strings" data-switchDecimal="' +
          decimal +
          '" data-value="' +
          validBetAmount +
          '">' +
          tcg.ac.currencyFormat(validBetAmount, decimal) +
          "</div>";
        var winAmt = api == "sett" ? list[i].winAmount : 0;
        var rowStatus = api == "sett" ? tcg.lib.prop("status_order_" + list[i].status) : "-";
        _html +=
          '    <div class="divTableCell ps-strings" data-switchDecimal="' +
          decimal +
          '" data-value="' +
          winAmt +
          '">' +
          tcg.ac.currencyFormat(winAmt, decimal) +
          "</div>";
        _html += '    <div class="divTableCell ps-strings" data-value="' + rowStatus + '">' + rowStatus + "</div>";
        _html += '    <div class="divTableCell ps-strings">' + list[i].orderNo + "</div>";
        _html += "</div>";
      }
      // SubTotal
      if (api == "sett") {
        $("#betHistoryTotal .subTotalBetAmt").text(tcg.ac.currencyFormat(footer.betAmount, decimal)).attr({
          "data-switchDecimal": decimal,
          "data-value": footer.betAmount,
        });
        $("#betHistoryTotal .subTotalWinAmt").text(tcg.ac.currencyFormat(footer.validBetAmount, decimal)).attr({
          "data-switchDecimal": decimal,
          "data-value": footer.validBetAmount,
        });
        $("#betHistoryTotal .subTotalPnl").text(tcg.ac.currencyFormat(footer.winAmount, decimal)).attr({
          "data-switchDecimal": decimal,
          "data-value": footer.winAmount,
        });
      } else {
        footer.validBetAmount = 0;
        footer.winAmount = 0;
        $("#betHistoryTotal .subTotalBetAmt").text(tcg.ac.currencyFormat(footer.betAmount, decimal)).attr({
          "data-switchDecimal": decimal,
          "data-value": footer.betAmount,
        });
        $("#betHistoryTotal .subTotalWinAmt").text(tcg.ac.currencyFormat(footer.validBetAmount, decimal)).attr({
          "data-switchDecimal": decimal,
          "data-value": footer.validBetAmount,
        });
        $("#betHistoryTotal .subTotalPnl").text(tcg.ac.currencyFormat(footer.winAmount, decimal)).attr({
          "data-switchDecimal": decimal,
          "data-value": footer.winAmount,
        });
      }
    } else {
      _html += "<div class='divTableRow dtr_noRecord'>";
      _html += "<div class='divTableCell ps-strings'>" + tcg.lib.prop("ac_reports_no_data") + "</div>";
      _html += "</div>";
    }
    return _html;
  };
  tcg.ac.ui.loadLottoGameHistory = function (result) {
    var _html = "",
      decimal = 2,
      orders = result.value.list;
    if (orders.length > 0) {
      for (var i = 0; i < orders.length; i++) {
        _html += "<div class='divTableRow clearfix' >";
        _html += "<div class='divTableCell ps-strings'>" + orders[i].gameNameCN + "</div>";
        _html +=
          "<div class='divTableCell ps-strings openItem' data-orderId='" +
          orders[i].orderId +
          "' data-chasing='" +
          orders[i].chase +
          "' data-orderMasterId=''>" +
          orders[i].abbrOrderNo +
          "</div>";
        _html += "<div class='divTableCell ps-strings'>" + orders[i].customerName + "</div>";
        var betTime = moment(orders[i].betTime)
          .format("YYYY-MM-DD HH:mm:ss")
          .replace(new Date().getFullYear().toString() + "-", "");
        _html += "<div class='divTableCell ps-strings'>" + betTime + "</div>";
        _html += "<div class='divTableCell ps-strings'>" + orders[i].numero + "</div>";
        var chase = orders[i].chase === false ? "-" : "是";
        // _html += "<div class='divTableCell ps-strings'>" + chase + "</div>";
        //_html += '<div class="divTableCell ps-strings yel-pop betContent">'+orders[i].bettingContent.substr(0,8)+'<div class="pop-yellow">'+tcg.ac.hoverLongText(orders[i].bettingContent, 10)+'</div></div>';
        _html +=
          "<div class='divTableCell ps-strings' data-switchDecimal='" +
          decimal +
          "' data-value='" +
          orders[i].betAmount +
          "'>" +
          tcg.ac.customCurrencyFormat(orders[i].betAmount, decimal) +
          "</div>";
        _html +=
          "<div class='divTableCell ps-strings' data-switchDecimal='" +
          decimal +
          "' data-value='" +
          orders[i].validBetAmount +
          "'>" +
          tcg.ac.customCurrencyFormat(orders[i].validBetAmount, decimal) +
          "</div>";
        if (orders[i].status == 5 || orders[i].status == 16) {
          _html +=
            "<div class='divTableCell ps-strings' data-switchDecimal='" +
            decimal +
            "' data-value='" +
            orders[i].winAmount +
            "'>" +
            tcg.ac.customCurrencyFormat(0, decimal) +
            "</div>";
        } else if (orders[i].status == 4) {
          _html +=
            "<div class='divTableCell ps-strings' data-switchDecimal='" +
            decimal +
            "' data-value='" +
            orders[i].winAmount +
            "'>" +
            tcg.ac.customCurrencyFormat(orders[i].winAmount, decimal) +
            "</div>";
        } else {
          _html += "<div class='divTableCell ps-strings' data-switchDecimal='" + decimal + "' data-value='-'>-</div>";
        }
        // _html += "<div class='divTableCell ps-strings' data-switchDecimal='" + decimal + "' data-value='" + orders[i].winAmount + "'>" + tcg.ac.customCurrencyFormat(orders[i].winAmount, decimal) + "</div>";
        _html +=
          "<div class='divTableCell ps-strings orderStatus'>" +
          tcg.lib.prop("orderStatus_" + orders[i].status) +
          "</div>";
        // var orderNumber = tcg.ac.shortenText(orders[i].orderNo);
        // if (orders[i].chase == true || orders[i].chase == "true") {
        //     orderNumber = orderNumber + "-" + tcg.ac.ui.strPad(orders[i].chasingOrder, 3);
        // }
        // _html += "<div class='divTableCell onel-cl-y ps-cen ps-strings yel-pop tbl-link'><span class='openItem' data-orderId='" + orders[i].orderDetailId + "' data-chasing='" + orders[i].chasing + "' data-orderMasterId='" + orders[i].orderMasterId + "'>" + orderNumber + "</span></div>";
        // _html += "<div class='divTableCell onel-cl-y ps-cen width-adjustment-128'>" + tcg.ac.timeToDateFormat(orders[i].bettingTime, "MonthDateTime") + "</div>";
        // _html += "<div class='divTableCell onel-cl-y ps-cen width-adjustment-128'>" + orders[i].numero + "</div>";
        // var chasingPhase;
        // if (orders[i].chasing == true) {
        //     if (orders[i].chasingOrder == 1) {
        //         chasingPhase = orders[i].chasingPhase + tcg.lib.prop("mc_lott_game_history_chase_suffix");
        //     } else {
        //         chasingPhase = tcg.lib.prop("mc_lott_game_history_no_chase");
        //     }
        // } else {
        //     chasingPhase = "-";
        // }
        // _html += "<div class='divTableCell onel-cl-y ps-cen div-y'>" + chasingPhase + "</div>";
        // _html += "<div class='divTableCell onel-cl-y' data-switchDecimal='" + decimal + "' data-value='" + orders[i].planBettingAmount + "'>" + tcg.ac.customCurrencyFormat(orders[i].planBettingAmount, decimal) + "</div>";
        // _html += "<div class='divTableCell onel-cl-y width-adjustment-80' data-switchDecimal='" + decimal + "' data-value='" + orders[i].actualBettingAmount + "'>" + tcg.ac.customCurrencyFormat(orders[i].actualBettingAmount, decimal) + "</div>";
        // if (orders[i].winningAmount == null) {
        //     // if (orders[i].orderStatus == 5 || orders[i].orderStatus == 2) {
        //         _html += "<div class='divTableCell onel-cl-y tbl-red width-adjustment-75 text-right ch-riht-rl'>-</div>";
        //     // }
        // } else {
        //     _html += "<div class='divTableCell onel-cl-y tbl-red width-adjustment-75 text-right ch-riht-rl' data-switchDecimal='" + decimal + "' data-value='" + orders[i].winningAmount + "'>" + tcg.ac.customCurrencyFormat(orders[i].winningAmount, decimal) + "</div>";
        // }
        // _html += "<div class='divTableCell onel-cl-y ps-cen div-z'>" + tcg.lib.prop("orderStatus_" + orders[i].orderStatus) + "</div>";
        _html += "</div>";
      }
    } else {
      _html += "<div class='tableContent-wrp'>";
      _html += "<div class='noResult-data'>" + tcg.lib.prop("no_result") + "</div>";
      _html += "</div>";
    }

    if (orders.length > 0) {
      var total = result.value.footer,
        _total = "";
      _total +=
        "<div class='tbl-total-con inline-block'>" + tcg.lib.prop("ac_lott_game_history_total_plan_bet_amt") + "</div>";
      _total +=
        "<div class='tbl-total-amt inline-block' data-switchDecimal='" +
        decimal +
        "' data-value='" +
        total.betAmount +
        "'>" +
        tcg.ac.customCurrencyFormat(total.betAmount, decimal) +
        "</div>";
      _total +=
        "<div class='tbl-total-con inline-block'>" +
        tcg.lib.prop("ac_lott_game_history_total_actual_bet_amt") +
        "</div>";
      _total +=
        "<div class='tbl-total-amt inline-block' data-switchDecimal='" +
        decimal +
        "' data-value='" +
        total.validBetAmount +
        "'>" +
        tcg.ac.customCurrencyFormat(total.validBetAmount, decimal) +
        "</div>";
      _total +=
        "<div class='tbl-total-con inline-block'>" + tcg.lib.prop("ac_lott_game_history_total_win_amt") + "</div>";
      _total +=
        "<div class='tbl-total-amt inline-block' data-switchDecimal='" +
        decimal +
        "' data-value='" +
        total.winAmount +
        "'>" +
        tcg.ac.customCurrencyFormat(total.winAmount, decimal) +
        "</div>";
      $("#lottoGameHistoryTotal").html(_total);
    }
    // $("#listWrapper input[name='order']").on("change", function () {
    //     $(this).val($(this).val().toUpperCase());
    // });

    return _html;
  };
  tcg.ac.ui.loadLottoGameHistoryItem = function (result, orderMasterId, chasing, orderInfo, icon) {
    var decimal = 4;
    globalVar.orderMasterId = orderMasterId;
    globalVar.orderMultiple = result.multiple;
    $("#itemWrapper .prsnl-game-name").html(
      "<span id='rm_" +
        result.gameCode +
        "' style='background-image: url(" +
        icon[result.gameCode] +
        ");background-size: 100%;background-repeat: no-repeat;background-position: center;'>&nbsp;</span>"
    );
    var orderNumber = tcg.ac.shortenText(result.orderNumber);
    if (!$("#chasing").hasClass("hide")) {
      $("#chasing").addClass("hide");
    }
    if (chasing == true || chasing == "true") {
      var chasingOrder = result.chasingOrder;
      orderNumber = orderNumber + "-" + tcg.ac.ui.strPad(chasingOrder, 3);
      $("#chasing").removeClass("hide");
    }
    $(".pkModeNote").addClass("hide");
    if (result.pkMode) {
      var pkModeWinProbability = result.pkModeWinProbability * 100 + "%";
      $(".pkModeNote").removeClass("hide");
      if (result.pkModeCalculationMode === "SPECIFY") {
        $("#pkModeWinProbability").text(pkModeWinProbability);
        $("#pkModeCalculationValue").text(result.pkModeCalculationValue.toFixed(2));
      }
      if (result.pkModeCalculationMode === "PERCENTAGE") {
        $("#pkModeWinProbability").text(pkModeWinProbability);
        $("#pkModeCalculationValue").text(result.pkModeCalculationValue + "%");
      }
    }
    $(".pkMode").show();
    $("#gh_orderNo").val(orderNumber);
    $("#itemWrapper #lotteryNum").removeClass("double");
    $("#itemWrapper .orderNo").text(orderNumber);
    $("#itemWrapper .seriesModel").text(result.series + "/" + tcg.lib.prop("bettingMode_" + result.bettingModeCode));
    if (result.orderStatus == 5 || result.orderStatus == 16) {
      $("#itemWrapper .prizeMoney").html(tcg.ac.customCurrencyFormat(0, 4));
    } else if (result.orderStatus == 4) {
      $("#itemWrapper .prizeMoney").html(tcg.ac.customCurrencyFormat(result.winningAmount, 4));
    } else {
      $("#itemWrapper .prizeMoney").html("-");
    }
    $("#itemWrapper .bettingNoteCount").text(result.stakes);
    $("#itemWrapper .bettingMultiples").html(tcg.ac.customCurrencyFormat(result.multiple));
    $("#itemWrapper .bettingTime").text(tcg.ac.timeToDateFormat(result.bettingTime, "dateTime"));
    $("#itemWrapper .orderStatus").text(tcg.lib.prop("orderStatus_" + result.orderStatus));
    $("#itemWrapper .bettingOnNumber").text(result.numero);
    var lotteryNumbers = result.winningNumber == null ? "-" : result.winningNumber;
    var splitChar = lotteryNumbers.indexOf(",") < 0 ? "" : ",";
    $("#itemWrapper #lotteryNum").html("<span>" + lotteryNumbers.split(splitChar).join("</span><span>") + "</span>");
    if (result.winningNumber != null) {
      $("#itemWrapper #lotteryNum").addClass("lotteryNumbers");
    }
    if (splitChar == ",") {
      $("#itemWrapper #lotteryNum").addClass("double");
    }

    var _html = "",
      pageNo = $("#itemWrapper [name='pageNo']");

    var orderInfos = orderInfo.content;
    for (var i = 0; i < orderInfos.length; i++) {
      _html += "<div class='divTableRow'>";
      _html +=
        "<div class='divTableCell onel-cl-y ps-cen yel-npop yel-pop playName'>" +
        lottTranslator.translatePlayName(orderInfos[i].playId, orderInfos[i].playCode, orderInfos[i].bettingContent);
      _html +=
        "<div class='pop-yellow' style='left:0'>" +
        lottTranslator.translatePlayName(orderInfos[i].playId, orderInfos[i].playCode, orderInfos[i].bettingContent) +
        "</div>";
      _html += "</div>";
      _html +=
        "<div class='divTableCell onel-cl-y ps-cen yel-pop  betContent'><span class='divTableCellnspan'>" +
        lottTranslator.translateBallContentText(orderInfos[i].playId, orderInfos[i].bettingContent) +
        "</span><div class='pop-yellow'>";
      _html += tcg.ac.hoverLongText(
        lottTranslator.translateBallContentText(orderInfos[i].playId, orderInfos[i].bettingContent),
        30
      );
      _html += "</div></div>";
      _html += "<div class='divTableCell onel-cl-y ps-cen '>" + orderInfos[i].stakes + "</div>"; // .xd2-xda
      var multiple = orderInfos[i].multiple * 1 * (globalVar.orderMultiple * 1);
      _html +=
        "<div class='divTableCell onel-cl-y ps-cen '>" +
        orderInfos[i].series +
        "/" +
        tcg.lib.prop("bettingMode_" + orderInfos[i].bettingMode) +
        "/" +
        multiple +
        "</div>";
      var total_bet_amt = orderInfos[i].bettingAmount * 1 * (globalVar.orderMultiple * 1);
      _html +=
        "<div class='divTableCell onel-cl-y ps-cen xg-xlass' data-switchDecimal='" +
        decimal +
        "' data-value='" +
        total_bet_amt +
        "'>" +
        tcg.ac.customCurrencyFormat(total_bet_amt, decimal) +
        "</div>";
      var winAmount;
      if (result.orderStatus == 5 || result.orderStatus == 16 || result.orderStatus == 4) {
        winAmount = 0;
        if (
          result.orderStatus == 4 &&
          (orderInfos[i].winAmount != null || orderInfos[i].winAmount != undefined || isNaN(orderInfos[i].winAmount))
        ) {
          winAmount = orderInfos[i].winAmount;
        }
      } else {
        winAmount = "-";
      }
      _html +=
        "<div class='divTableCell onel-cl-y ps-cen xg-xlass' data-switchDecimal='" +
        decimal +
        "' data-value='" +
        winAmount +
        "'>" +
        tcg.ac.currencyFormat(winAmount, decimal) +
        "</div>";
      var pkMode = result.pkMode ? "是" : "否";
      if (
        result.pkModeCalculationMode !== null ||
        result.pkModeCalculationValue !== null ||
        result.pkModeWinProbability !== null
      ) {
        _html += "<div class='divTableCell onel-cl-y ps-cen pkMode'>" + pkMode + "</div>";
      }
      _html += "</div>";
    }
    if (
      result.pkModeCalculationMode === null ||
      result.pkModeCalculationValue === null ||
      result.pkModeWinProbability === null
    ) {
      $(".pkMode").hide();
    }
    if (result.orderStatus == 2) {
      $("#cancelGameHistoryDetail").parent().removeClass("hide");
      $("#cancelGameHistoryDetail").parent().show();
    } else {
      $("#cancelGameHistoryDetail").parent().hide();
    }
    return _html;
  };
  tcg.ac.ui.loadChaseRecords = function (result) {
    var decimal = 2,
      orders = result.orders.content,
      _html = "";
    if (orders.length > 0) {
      for (var i = 0; i < orders.length; i++) {
        var orderNumber = tcg.ac.shortenText(orders[i].orderNumber);
        if (orders[i].chasing == true || orders[i].chasing == "true") {
          orderNumber = orderNumber + "-" + tcg.ac.ui.strPad(orders[i].chasingOrder, 3);
        }
        _html += "<div class='divTableRow'>";
        _html +=
          "<div class='divTableCell onel-cl-y ps-cen openItem ps-strings tbl-link' data-chasingOrder='" +
          orders[i].chasingOrder +
          "' data-orderMasterId='" +
          orders[i].orderMasterId +
          "'>" +
          orderNumber +
          "</div>";
        _html +=
          "<div class='divTableCell onel-cl-y ps-cen'>" + tcg.lib.prop("gameName_" + orders[i].gameCode) + "</div>";
        _html += "<div class='divTableCell onel-cl-y ps-cen'>" + orders[i].numero + "</div>";
        _html += "<div class='divTableCell onel-cl-y ps-cen'>" + orders[i].chaseEndNumero + "</div>";
        _html += "<div class='divTableCell onel-cl-y ps-cen div-y'>" + orders[i].chasingPhase + "</div>";
        _html += "<div class='divTableCell onel-cl-y ps-cen div-y'>" + orders[i].actualChasingPhase + "</div>";
        _html +=
          "<div class='divTableCell onel-cl-y ps-num' data-switchDecimal='" +
          decimal +
          "' data-value='" +
          orders[i].planBettingAmount +
          "'>" +
          tcg.ac.customCurrencyFormat(orders[i].planBettingAmount, decimal) +
          "</div>";
        var winningAmount = orders[i].winningAmount == null ? 0 : orders[i].winningAmount,
          winningAmountColor = orders[i].orderStatus == 4 ? "tbl-red" : "";
        _html +=
          "<div class='divTableCell onel-cl-y ps-num " +
          winningAmountColor +
          "' data-switchDecimal='" +
          decimal +
          "' data-value='" +
          winningAmount +
          "'>" +
          tcg.ac.customCurrencyFormat(winningAmount, decimal) +
          "</div>";
        _html +=
          "<div class='divTableCell onel-cl-y ps-cen div-x " +
          winningAmountColor +
          "'>" +
          tcg.lib.prop("orderStatus_" + orders[i].orderStatus) +
          "</div>";
        _html += "</div>";
      }
    } else {
      _html += "<div class='tableContent-wrp'>";
      _html += "<div class='noResult-data'>" + tcg.lib.prop("no_result") + "</div>";
      _html += "</div>";
    }
    var totalPlanBettingAmt = result.orderSumTO.sumPlanBettingAmt;
    var totalActualBetAmount = result.orderSumTO.sumActualBettingAmt;
    var totalWinningAmount = result.orderSumTO.sumWinningAmt;
    $("#totalPlanBettingAmt").html(tcg.ac.customCurrencyFormat(totalPlanBettingAmt, decimal)).attr({
      "data-switchDecimal": decimal,
      "data-value": totalPlanBettingAmt,
    });
    $("#totalActualBettingAmt").html(tcg.ac.customCurrencyFormat(totalActualBetAmount, decimal)).attr({
      "data-switchDecimal": decimal,
      "data-value": totalActualBetAmount,
    });
    $("#totalWinningAmt").html(tcg.ac.customCurrencyFormat(totalWinningAmount, decimal)).attr({
      "data-switchDecimal": decimal,
      "data-value": totalWinningAmount,
    });

    return _html;
  };
  tcg.ac.ui.loadChaseRecordItem = function (result, chasingOrder, orderInfo, icon) {
    var decimal = 4;

    $("#chase_itemWrapper .prsnl-game-name").html(
      "<span id='rm_" +
        result.gameCode +
        "' style='background-image: url(" +
        icon[result.gameCode] +
        ");background-size: 100%;background-repeat: no-repeat;background-position: center;'>&nbsp;</span>"
    );

    var orderNumber = tcg.ac.shortenText(result.orderNumber);
    if (result.chase == true || result.chase == "true") {
      orderNumber = orderNumber + "-" + tcg.ac.ui.strPad(chasingOrder, 3);
    }

    $("#ch_orderNo").val(orderNumber);
    $("#chase_itemWrapper .orderNo").text(orderNumber);
    $("#chase_itemWrapper .seriesModel").text(result.series + "/" + tcg.lib.prop("bettingMode_" + result.mode)); //TCG.Prop("bettingMode_"+result.bettingModeCode)

    var winningAmount = 0;
    $.each(result.orderDetailOrders, function (index, value) {
      if (value.winningAmount != undefined) {
        winningAmount += value.winningAmount;
      }
    });

    $("#chase_itemWrapper .prizeMoney").html(tcg.ac.customCurrencyFormat(winningAmount, decimal));
    $("#chase_itemWrapper .bettingNoteCount").text(result.orderDetailOrders[0].stakes); //
    var multiples = result.orderDetailOrders[0].multiple;
    $("#chase_itemWrapper .bettingTime").text(tcg.ac.timeToDateFormat(result.bettingTime, "dateTime"));
    $("#chase_itemWrapper .orderStatus").text(tcg.lib.prop("orderStatus_" + result.orderStatus));
    $("#chase_itemWrapper .bettingOnNumber").text(result.numero);
    $("#chase_itemWrapper .lotteryNumbers").text(result.winningNumber == null ? "-" : result.winningNumber);
    var winningStop = result.winningStop == true ? tcg.lib.prop("mc_cno_item_yes") : tcg.lib.prop("mc_cno_item_no");
    $("#chase_itemWrapper .winningstop").text(winningStop);
    $("#chase_itemWrapper .plan_bet_amount").html(tcg.ac.customCurrencyFormat(result.totalBetAmount, decimal));
    var abandoning = result.abandoning == true ? tcg.lib.prop("mc_cno_item_yes") : tcg.lib.prop("mc_cno_item_no");
    $("#chase_itemWrapper .abonding").text(abandoning);
    $("#chase_itemWrapper .actual_bet_amount").html(tcg.ac.customCurrencyFormat(result.totalActualBetAmount, decimal));
    var completed = result.completed == true ? tcg.lib.prop("mc_cno_item_yes") : tcg.lib.prop("mc_cno_item_no");
    $("#chase_itemWrapper .chasephase").text(result.totalChasePhases);
    $("#chase_itemWrapper .isCompleted").text(completed);

    var tab1_html = "",
      orderDetails = result.orderDetailOrders,
      tab2_html = "";
    globalVar.orderMasterId = result.orderMasterId;
    var pageNo = $("#chase_itemWrapper [name='pageNo']");

    var orderInfos = orderInfo.content;
    for (var i = 0; i < orderInfos.length; i++) {
      var decimal = 2;
      tab1_html += "<div class='divTableRow'>";
      tab1_html +=
        "<div class='divTableCell onel-cl-y ps-cen yel-pop'>" +
        lottTranslator.translatePlayName(orderInfos[i].playId, orderInfos[i].playCode, orderInfos[i].bettingContent);
      tab1_html +=
        "<div class='pop-yellow' style='left:30px'>" +
        lottTranslator.translatePlayName(orderInfos[i].playId, orderInfos[i].playCode, orderInfos[i].bettingContent) +
        "</div>";
      tab1_html += "</div>";
      tab1_html +=
        "<div class='divTableCell onel-cl-y ps-cen yel-pop ps-strings'>" +
        tcg.ac.strSub(lottTranslator.translateBallContentText(orderInfos[i].playId, orderInfos[i].bettingContent), 10) +
        "<div class='pop-yellow txbx1'>";
      tab1_html += tcg.ac.hoverLongText(
        lottTranslator.translateBallContentText(orderInfos[i].playId, orderInfos[i].bettingContent),
        10
      );
      tab1_html += "</div></div>";
      tab1_html += "<div class='divTableCell onel-cl-y ps-cen'>" + orderInfos[i].stakes + "</div>";
      var multiple = orderInfos[i].multiple * 1 * (result.orderDetailOrders[0].multiple * 1);
      tab1_html +=
        "<div class='divTableCell onel-cl-y ps-cen2'>" +
        orderInfos[i].series +
        "/" +
        tcg.lib.prop("bettingMode_" + orderInfos[i].bettingMode) +
        "/" +
        multiple +
        "</div>";
      var total_bet_amt = orderInfos[i].bettingAmount * 1 * (result.orderDetailOrders[0].multiple * 1);
      tab1_html +=
        "<div class='divTableCell onel-cl-y ps-num' data-switchDecimal='" +
        decimal +
        "' data-value='" +
        total_bet_amt +
        "'>" +
        tcg.ac.customCurrencyFormat(total_bet_amt, decimal) +
        "</div>";
      tab1_html += "</div>";
    }
    $("#orderInfos").html(tab1_html);

    tcg.ac.ui.loadPagination("chaseRecordItem", pageNo.val(), orderInfo.totalPages, "#ncoDetailPagination");

    var orderDet = [];
    for (var i = 0; i < orderDetails.length; i++) {
      for (var n = 0; n < orderDetails.length; n++) {
        if (orderDetails[n].chasingOrder * 1 == i + 1) {
          orderDet[i] = orderDetails[n];
          break;
        }
      }
    }
    for (var i = 0; i < orderDetails.length; i++) {
      tab2_html += "<div class='divTableRow orderDetailsItem'>";
      tab2_html += "<div class='divTableCell onel-cl-y ps-cen div-x col-checkbox'>"; //ps-chkd
      if (orderDetails[i].orderStatus == 2) {
        tab2_html +=
          "<input type='checkbox' id='check" +
          i +
          "' name='orderDetail' value='" +
          orderDetails[i].orderDetailId +
          "' />";
        tab2_html += "<label for='check" + i + "'></label>";
      }
      tab2_html += "</div>";
      tab2_html += "<div class='divTableCell onel-cl-y ps-cen'>" + orderDetails[i].numero + "</div>";
      var winningNumber = orderDetails[i].winningNumber == null ? "-" : orderDetails[i].winningNumber;
      tab2_html +=
        "<div class='divTableCell onel-cl-y ps-cen yel-pop ps-strings'>" +
        tcg.ac.strSub(winningNumber, 10) +
        "<div class='pop-yellow txbx1 chUn123'>";
      tab2_html += tcg.ac.hoverLongText(winningNumber, 10);
      tab2_html += "</div></div>";
      tab2_html +=
        "<div class='divTableCell onel-cl-y ps-cen div-x col-orderStatus'>" +
        tcg.lib.prop("orderStatus_" + orderDetails[i].orderStatus) +
        "</div>";
      var bettingSumAmount = orderDetails[i].bettingSumAmount == null ? 0 : orderDetails[i].bettingSumAmount * 1;
      tab2_html +=
        "<div class='divTableCell onel-cl-y ps-num' data-switchDecimal='" +
        decimal +
        "' data-value='" +
        bettingSumAmount +
        "'>" +
        tcg.ac.customCurrencyFormat(bettingSumAmount, decimal) +
        "</div>";
      var winningAmount = orderDetails[i].winningAmount == null ? 0 : orderDetails[i].winningAmount * 1,
        winningAmountClass = orderDetails[i].orderStatus == 4 ? "tbl-red" : "";
      tab2_html +=
        "<div class='divTableCell onel-cl-y ps-num " +
        winningAmountClass +
        "' data-switchDecimal='" +
        decimal +
        "' data-value='" +
        winningAmount +
        "'>" +
        tcg.ac.customCurrencyFormat(winningAmount, decimal) +
        "</div>";
      tab2_html +=
        "<div class='divTableCell onel-cl-y ps-cen div-x blue openGameHistoryItem' id='openGameHistoryItem' data-orderId='" +
        orderDetails[i].orderDetailId +
        "' data-chasing='true'>" +
        tcg.lib.prop("mc_cno_open_item_btn") +
        "</div>";
      tab2_html += "</div>";
    }
    $("#orderDetailOrders").html(tab2_html);
  };

  tcg.ac.ui.myIncome3 = function (gameCategory, result) {
    var datalist = [];
    var enabledDividend = JSON.parse(sessionStorage.consolidated).value.divtypeinfo
      ? Object.keys(JSON.parse(sessionStorage.consolidated).value.divtypeinfo)
      : [];
    var enabledSalary = JSON.parse(sessionStorage.consolidated).value.dailysalinfo
      ? Object.keys(JSON.parse(sessionStorage.consolidated).value.dailysalinfo)
      : [];
    if ("ALL".indexOf(gameCategory) > -1) {
      tcg.apis.getGameMakers(tcg.ac.getMerchant(), sessionStorage.token, function (result) {
        var available = Object.keys(result.value.content);

        datalist = ["balanceDate", "lottIncome", "rngIncome", "liveIncome", "sportsIncome", "fishIncome", "pvpIncome"];

        if (available.indexOf("LOTT") == -1) {
          datalist.splice(1, 1);
        }
        if (available.indexOf("PVP") == -1) {
          datalist.splice(6, 1);
        }
        if (available.indexOf("RNG") == -1) {
          datalist.splice(2, 1);
        }
        if (available.indexOf("LIVE") == -1) {
          datalist.splice(3, 1);
        }
        if (available.indexOf("FISH") == -1) {
          datalist.splice(5, 1);
        }
        if (available.indexOf("SPORTS") == -1) {
          datalist.splice(4, 1);
        }
        // if(available.indexOf("ELOTT") == -1 ) {
        //      datalist.splice(1,1);
        // }
      });

      //datalist =  ["balanceDate" , "lottIncome" , "rngIncome" , "liveIncome" , "sportsIncome"  ,"totalIncome"];
    } else if ("LOTT".indexOf(gameCategory) > -1) {
      datalist = ["balanceDate", "totalAgentRebate"];
      if (sessionStorage.enableSalary == "true") {
        datalist.push("totalDailySalary");
        $("tr[data-obj='lott'] .mac-table-head.salary").removeClass("hide");
      } else {
        $("tr[data-obj='lott'] .mac-table-head.salary").addClass("hide");
      }
      if (sessionStorage.enableContract == "true" && enabledDividend.indexOf(gameCategory) > -1) {
        datalist.push("totalDividend");
        $("tr[data-obj='lott'] .mac-table-head.contract").removeClass("hide");
      } else {
        $("tr[data-obj='lott'] .mac-table-head.contract").addClass("hide");
      }
    } else if ("RNG||LIVE||SPORTS||FISH".indexOf(gameCategory) > -1) {
      datalist = ["balanceDate", "totalAgentRebate"];
      if (sessionStorage.enableContract == "true" && enabledDividend.indexOf(gameCategory) > -1) {
        datalist.push("totalDividend");
        $("tr[data-obj='rng||live||sports||fish'] .mac-table-head.contract").removeClass("hide");
      } else {
        $("tr[data-obj='rng||live||sports||fish'] .mac-table-head.contract").addClass("hide");
      }
    } else if ("PVP".indexOf(gameCategory) > -1) {
      datalist = ["balanceDate", "totalAgentRebate"];
    }
    datalist.push("totalIncome");
    var _html = "",
      _obj = {};
    if (result.value) {
      _obj = result.value.list;
      $.each(_obj, function (index, value) {
        _html += '<tr class="mac-table-row">';
        for (var k in datalist) {
          _html += '<td class="mac-table-cell">' + value[datalist[k]] + "</td>";
        }
        _html += "</tr>";
      });
    } else {
      _html = '<div class="tableContent-wrp"><div class="noResult-data">没有查到符合条件的数据</div></div>';
      console.error("ODS is currently down or slow due to number of requests.");
    }

    return _html;
  };
  tcg.ac.ui.myIncome3_footer = function (gameCategory, result) {
    if ("ALL".indexOf(gameCategory) > -1) {
      datalist = ["lottIncome", "rngIncome", "liveIncome", "sportsIncome", "fishIncome", "pvpIncome"];

      //datalist =  ["balanceDate" , "lottIncome" , "rngIncome" , "liveIncome" , "sportsIncome"  ,"totalIncome"];
    } else if ("LOTT".indexOf(gameCategory) > -1) {
      datalist = ["totalAgentRebate"];
      if (sessionStorage.enableSalary == "true") {
        datalist.push("totalDailySalary");
        $(".mac-table-head.salary").removeClass("hide");
      } else {
        $(".mac-table-head.salary").addClass("hide");
      }
      if (sessionStorage.enableContract == "true") {
        datalist.push("totalDividend");
        $(".mac-table-head.contract").removeClass("hide");
      } else {
        $(".mac-table-head.contract").addClass("hide");
      }
    } else if ("RNG||LIVE||SPORTS||FISH".indexOf(gameCategory) > -1) {
      datalist = ["totalAgentRebate"];
      if (sessionStorage.enableContract == "true") {
        datalist.push("totalDividend");
      }
    } else if ("PVP".indexOf(gameCategory) > -1) {
      datalist = ["totalAgentRebate"];
    }
    datalist.push("totalIncome");
    var _html = "",
      _obj = {},
      consolidatedData = {};
    if (result.value) {
      if (result.value.footer) {
        _obj = result.value.footer;
        $(".mac-table-row").each(function (index, value) {
          if (!$(value).hasClass("hide") && $(value).attr("data-obj")) {
            $(value)
              .find("th")
              .each(function (indexOfHeader, valueOfHeader) {
                if (indexOfHeader != 0 && !$(valueOfHeader).hasClass("hide")) {
                  consolidatedData[$(valueOfHeader).html()] = 0.0;
                  consolidatedData[$(valueOfHeader).html()] += parseFloat(_obj[datalist[indexOfHeader - 1]]);
                }
              });
          }
        });
      }

      console.log(consolidatedData);
      if (!("ALL".indexOf(gameCategory) > -1)) {
        _html = '<div class="mac-foot-field title"><label>总汇：</label><span></span> </div>';
      }

      $.each(consolidatedData, function (key, value) {
        _html +=
          '<div class="mac-foot-field"><label>' +
          key +
          "：</label><span>" +
          tcg.ac.customCurrencyFormat(value, 2) +
          "</span></div>";
      });
      return _html;
    } else {
    }
  };
  // Team Income Report
  tcg.ac.ui.searchAgentTeamIncome = function () {
    $(document)
      .off("click", "#agentTeamIncome .form-submit")
      .on("click", "#agentTeamIncome .form-submit", function (event) {
        //$("#agentTeamIncome .breadcrumbs .b-list.new-entry").remove();
        //tcg.ac.getAgentTeamIncome();
        if (event.originalEvent === undefined) {
          var len = $("#agentTeamIncome .b-list").length - 1;
          var customerId = $($("#agentTeamIncome .breadcrumbs .b-list")[len]).attr("data-customerid");
          tcg.ac.getAgentTeamIncome(customerId);
        } else {
          $("#rngAgentPnlForm .breadcrumbs .b-list.new-entry").remove();
          tcg.ac.getAgentTeamIncome();
        }
      });
  };
  tcg.ac.ui.loadAgentTeamIncome = function (result) {
    var _column = "";
    _column +=
      "<div class='divTableCell onel-th-x'>" +
      tcg.lib.prop("ac_subordinate_management_member_management_username_label") +
      "</div>";
    _column += "<div class='divTableCell onel-th-x'>" + tcg.lib.prop("ac_revenue_report_column_2") + "</div>";
    _column +=
      "<div class='divTableCell onel-th-x dailySalary'>" + tcg.lib.prop("ac_revenue_report_column_3") + "</div>";
    _column += "<div class='divTableCell onel-th-x'>" + tcg.lib.prop("ac_revenue_report_column_4") + "</div>";
    _column += "<div class='divTableCell onel-th-x'>" + tcg.lib.prop("ac_revenue_report_column_5") + "</div>";
    $("#agentTeamIncomeColumn").html(_column);

    var decimal = 2,
      list = result.list,
      _html = "";
    for (var i = 0; i < list.length; i++) {
      if (i % 2 == 0) {
        _html += "<div class='divTableRow'>";
      } else {
        _html += "<div class='divTableRow divTableRowothr'>";
      }
      var account = list[i].customer_name.split("@");
      _html +=
        "<div class='divTableCell tbl-link ps-strings' data-customerId='" +
        list[i].customer_id +
        "'>" +
        account[1] +
        "</div>";
      var agent_commission =
        list[i].live_agent_commission +
        list[i].lott_agent_commission +
        list[i].pvp_commission +
        list[i].rng_agent_commission;
      _html +=
        "<div class='divTableCell ps-num' data-switchDecimal='" +
        decimal +
        "' data-value='" +
        agent_commission +
        "'>" +
        tcg.ac.customCurrencyFormat(agent_commission, decimal) +
        "</div>";
      var lott_daily_income = list[i].lott_daily_income;
      _html +=
        "<div class='divTableCell ps-num dailySalary' data-switchDecimal='" +
        decimal +
        "' data-value='" +
        lott_daily_income +
        "'>" +
        tcg.ac.customCurrencyFormat(lott_daily_income, decimal) +
        "</div>";
      var lott_brokerage = list[i].lott_brokerage;
      _html +=
        "<div class='divTableCell ps-num' data-switchDecimal='" +
        decimal +
        "' data-value='" +
        lott_brokerage +
        "'>" +
        tcg.ac.customCurrencyFormat(lott_brokerage, decimal) +
        "</div>";
      var agentIncomeTotal = agent_commission + lott_daily_income + lott_brokerage;
      _html +=
        "<div class='divTableCell ps-num ' data-switchDecimal='" +
        decimal +
        "' data-total='" +
        decimal +
        "' data-value='" +
        agentIncomeTotal +
        "'>" +
        tcg.ac.customCurrencyFormat(agentIncomeTotal, decimal) +
        "</div>";
      _html += "</div>";
    }
    $("#agentTeamIncomeList").html(_html);
  };
  tcg.ac.ui.switchAgentTeamIncome = function () {
    $(document)
      .off("click", "#agentTeamIncome .summaryTypeList li")
      .on("click", "#agentTeamIncome .summaryTypeList li", function () {
        if (!$(this).hasClass("active") && !$("#lottoAgentPnlForm .form-submit").hasClass("processing")) {
          var rel = $(this).attr("data-rel");
          $("#agentTeamIncome .summaryTypeList li").removeClass("active");
          $(this).addClass("active");
          $("#agentTeamIncome [name='summaryType']").val(rel);
          $("#agentTeamIncome .form-submit").click();
        }
      });
  };
  tcg.ac.ui.selectAgentTeamIncome = function () {
    $(document)
      .off("click", "#agentTeamIncomeList .tbl-link")
      .on("click", "#agentTeamIncomeList .tbl-link", function () {
        if (!$(this).hasClass("loading")) {
          var customerName = $(this).text(),
            customerId = $(this).attr("data-customerId"),
            isOpen = false;
          $("#agentTeamIncome .breadcrumbs .b-list").each(function () {
            if ($(this).attr("data-customerId") == customerId) {
              isOpen = true;
            }
          });
          if (!isOpen) {
            $("#agentTeamIncome .breadcrumbs .b-list").addClass("loading");
            $("#agentTeamIncome .summaryTypeList li").addClass("loading");
            _html =
              "<div class='arrow-small-con mem-icon2 inline-block b-list new-entry' data-customerId='" +
              customerId +
              "'>" +
              customerName +
              "</div>";
            $("#agentTeamIncome .breadcrumbs").append(_html);
          }
          $("#agentTeamIncome [name='pageNo']").val(1);
          tcg.ac.getAgentTeamIncome(customerId);
        }
      });
  };
  tcg.ac.ui.clickAgentTeamIncome = function () {
    $(document)
      .off("click", "#agentTeamIncome .breadcrumbs .b-list")
      .on("click", "#agentTeamIncome .breadcrumbs .b-list", function () {
        if (!$(this).hasClass("loading")) {
          var customerId = $(this).attr("data-customerId"),
            customerName = $(this).text(),
            removeFromList = false;
          $("#agentTeamIncome .breadcrumbs .b-list").each(function () {
            if (removeFromList) $(this).remove();
            if ($(this).attr("data-customerId") == customerId) {
              removeFromList = true;
            }
          });
          $("#agentTeamIncome .breadcrumbs .b-list").addClass("loading");
          $("#agentTeamIncome .summaryTypeList li").addClass("loading");
          tcg.ac.getAgentTeamIncome(customerId);
        }
      });
  };

  // Agent Revenue Report
  tcg.ac.ui.searchRevenueReport = function () {
    $(document)
      .off("click", "#revenueReportForm .form-submit")
      .on("click", "#revenueReportForm .form-submit", function () {
        tcg.ac.getRevenueReport();
      });
  };
  tcg.ac.ui.loadRevenueReport = function (result) {
    console.log(result);
    var list = result.list,
      total = result.footer,
      decimal = 2,
      _html = "",
      _total = "";
    for (var i = 0; i < list.length; i++) {
      var dateTime = list[i].balance_date.split(" "),
        elott_agent_commission = list[i].elott_agent_commission == "" ? 0 : list[i].elott_agent_commission,
        fish_agent_commission = list[i].fish_agent_commission == "" ? 0 : list[i].fish_agent_commission,
        live_agent_commission = list[i].live_agent_commission == "" ? 0 : list[i].live_agent_commission,
        lott_agent_commission = list[i].lott_agent_commission == "" ? 0 : list[i].lott_agent_commission,
        pvp_commission = list[i].pvp_commission == "" ? 0 : list[i].pvp_commission,
        rng_agent_commission = list[i].rng_agent_commission == "" ? 0 : list[i].rng_agent_commission,
        sports_agent_commission = list[i].sports_agent_commission == "" ? 0 : list[i].sports_agent_commission,
        agent_commission =
          elott_agent_commission +
          fish_agent_commission +
          live_agent_commission +
          lott_agent_commission +
          pvp_commission +
          rng_agent_commission +
          sports_agent_commission,
        dailySalary = list[i].lott_daily_income == "" ? 0 : list[i].lott_daily_income,
        brokerage = list[i].lott_brokerage == "" ? 0 : list[i].lott_brokerage,
        totalIncome = list[i].agent_income == "" ? 0 : list[i].agent_income;
      var keys = Object.keys(list[i]),
        totalBrokerage;
      for (var j = 0; j < keys.length; j++) {
        if (keys[j].indexOf("profit_sharing") > -1) {
          totalBrokerage += list[i][keys[j]];
        }
      }
      totalBrokerage += brokerage;
      _html += "<div class='arr-tr clearfix'>";
      _html += "<div class='arr-td'>" + dateTime[0] + "</div>";
      _html +=
        "<div class='arr-td amount-fixCh' data-switchDecimal='" +
        decimal +
        "' data-value='" +
        agent_commission +
        "'>" +
        tcg.ac.customCurrencyFormat(agent_commission, decimal) +
        "</div>";
      _html +=
        "<div class='arr-td amount-fixCh' data-switchDecimal='" +
        decimal +
        "' data-value='" +
        dailySalary +
        "'>" +
        tcg.ac.customCurrencyFormat(dailySalary, decimal) +
        "</div>";
      _html +=
        "<div class='arr-td amount-fixCh' data-switchDecimal='" +
        decimal +
        "' data-value='" +
        totalBrokerage +
        "'>" +
        tcg.ac.customCurrencyFormat(totalBrokerage, decimal) +
        "</div>";
      _html +=
        "<div class='arr-td amount-fixCh' data-switchDecimal='" +
        decimal +
        "' data-value='" +
        totalIncome +
        "'>" +
        tcg.ac.customCurrencyFormat(totalIncome, decimal) +
        "</div>";
      _html += "</div>";
    }
    $("#revenueReportList").html(_html);

    var total_elott_agent_commission =
        list.length == 0 || total.elott_agent_commission == "" ? 0 : total.elott_agent_commission,
      total_fish_agent_commission =
        list.length == 0 || total.fish_agent_commission == "" ? 0 : total.fish_agent_commission,
      total_live_agent_commission =
        list.length == 0 || total.live_agent_commission == "" ? 0 : total.live_agent_commission,
      total_lott_agent_commission =
        list.length == 0 || total.lott_agent_commission == "" ? 0 : total.lott_agent_commission,
      total_agent_commission =
        total_elott_agent_commission +
        total_fish_agent_commission +
        total_live_agent_commission +
        total_lott_agent_commission,
      total_dailySalary = list.length == 0 || total.lott_daily_income == "" ? 0 : total.lott_daily_income,
      total_brokerage = list.length == 0 || total.lott_brokerage == "" ? 0 : total.lott_brokerage,
      total_income = list.length == 0 || total.agent_income == "" ? 0 : total.agent_income;
    _total +=
      "<span class='sdt'>" +
      tcg.lib.prop("ac_rebate_total") +
      ":</span><span class='sdd' data-switchDecimal='" +
      decimal +
      "' data-value='" +
      total_agent_commission +
      "'>" +
      tcg.ac.customCurrencyFormat(total_agent_commission, decimal) +
      "</span>";
    _total +=
      "<span class='sdt'>" +
      tcg.lib.prop("ac_daily_salary") +
      ":</span><span class='sdd' data-switchDecimal='" +
      decimal +
      "' data-value='" +
      total_dailySalary +
      "'>" +
      tcg.ac.customCurrencyFormat(total_dailySalary, decimal) +
      "</span>";
    _total +=
      "<span class='sdt'>" +
      tcg.lib.prop("ac_total_commission") +
      ":</span><span class='sdd' data-switchDecimal='" +
      decimal +
      "' data-value='" +
      total_brokerage +
      "'>" +
      tcg.ac.customCurrencyFormat(total_brokerage, decimal) +
      "</span>";
    _total +=
      "<span class='sdt'>" +
      tcg.lib.prop("ac_total_amount_income") +
      ":</span><span class='sdd' data-switchDecimal='" +
      decimal +
      "' data-value='" +
      total_income +
      "'>" +
      tcg.ac.customCurrencyFormat(total_income, decimal) +
      "</span>";
    $("#revenueReportTotal").html(_total);
  };

  // Agent Dividend
  tcg.ac.ui.loadAgentDividendRecord = function (result) {
    if (result.list.length == 0) return;
    var prev_commission_period =
      tcg.ac.timeToDateFormat(result.list[0].prev_start_date.time, "date") +
      "~" +
      tcg.ac.timeToDateFormat(result.list[0].prev_end_date.time, "date");
    $(".prev_commission_period").text(prev_commission_period);
    var prev_pnl = result.list[0].prev_net_profit * 1;
    var prev_pnlColor = prev_pnl >= 0 ? "tbl-red" : "tbl-green";
    $(".prev_pnl").text(prev_pnl).addClass(prev_pnlColor);
    var prev_commission = result.list[0].prev_dividend_income;
    $(".prev_commission").text(prev_commission);
    var current_commission_period =
      tcg.ac.timeToDateFormat(result.list[0].curr_start_date.time, "date") +
      "~" +
      tcg.ac.timeToDateFormat(result.list[0].curr_end_date.time, "date");
    $(".current_commission_period").text(current_commission_period);
    var current_pnl = result.list[0].curr_net_profit * 1;
    var current_pnlColor = current_pnl >= 0 ? "tbl-red" : "tbl-green";
    $(".current_pnl").text(current_pnl).addClass(current_pnlColor);
    var current_commission = result.list[0].curr_dividend_income;
    $(".current_commission").text(current_commission);
    var commission_rate = result.list[0].prev_dividend_rate + "%";
    $(".commission_rate").text(commission_rate);
    var count_of_commission_computed = result.list[0].commission_distributed_count;
    $(".count_of_commission_computed").text(count_of_commission_computed);
    var count_of_commission_disbursed = result.list[0].commission_received_count;
    $(".count_of_commission_disbursed").text(count_of_commission_disbursed);
  };
  tcg.ac.ui.switchAgentDividendTab = function () {
    $("#switchAgentDividend li")
      .unbind("click")
      .bind("click", function () {
        var targetId = $(this).attr("data-rel");
        $("#switchAgentDividend li.active").removeClass("active");
        $(this).addClass("active");
        $(".contentTab").addClass("hide");
        $("#" + targetId).removeClass("hide");

        switch (targetId) {
          case "contractManagementContent":
            $("#editContract").addClass("hide");
            $("#contractManagement").removeClass("hide");
            tcg.ac.getReceiverScreen();
            break;
          case "disbursementContent":
            $("#disbursementContent").removeClass("hide");
            $("#disbursementContent .form-submit").click();
            break;
        }
      });
  };
  tcg.ac.ui.loadDisbursementRecords = function (result) {
    var _html = "";
    if (result.value.list) {
      result = result.value.list;
      if (result.length > 0) {
        for (var i = 0; i < result.length; i++) {
          var elem = result[i];
          var action = "";
          if (elem.status === "0" && elem.amount > 0) {
            action =
              '<div class="hag_link_mangdetl tbl-link action-btn disburse" id="' +
              elem.id +
              '" data-beneficiaryId="' +
              elem.beneficiary_id +
              '">派发</div>';
          } else {
            action = '<div class="" id="' + elem.id + '" data-beneficiaryId="' + elem.beneficiary_id + '">-</div>';
          }
          _html += '   <div class="distbursement-content">';
          _html += '   <div class="d-name">' + elem.beneficiary_name + "</div>";
          _html += '   <div class="d-date">' + tcg.ac.formatDateFull(elem.create_date, "yyyy-MM-dd") + "</div>";
          _html += '   <div class="d-Tbeating">' + elem.team_bet + "</div>";
          _html += '   <div class="d-TPNL">' + elem.team_profit + "</div>";
          _html += '   <div class="d-active-member">' + elem.active_member + "</div>";
          _html += '   <div class="d-ratio">' + Math.round(elem.contract_rate * 100, 2) + "%</div>";
          _html += '   <div class="d-amount">' + elem.amount + "</div>";
          _html += action;
          _html += "   </div>";
        }
      } else {
        _html += "<div class='tableContent-wrp'><div class='noResult-data'>" + tcg.lib.prop("no_result") + "</div>";
      }
    } else {
      _html += "<div class='tableContent-wrp'><div class='noResult-data'>" + tcg.lib.prop("no_result") + "</div>";
    }

    $("#tblDisbursementRecords").html(_html);
  };
  tcg.ac.ui.loadDisbursementRecordsCopy = function (result) {
    var _html = "";
    if (result.value.list) {
      result = result.value.list;
      if (result.length > 0) {
        for (var i = 0; i < result.length; i++) {
          var elem = result[i];
          var action = "";
          if (elem.status === "0" && elem.amount > 0) {
            action =
              '<div class="hag_link_mangdetl tbl-link action-btn disburse" id="' +
              elem.id +
              '" data-beneficiaryId="' +
              elem.beneficiary_id +
              '">派发</div>';
          } else {
            action = '<div class="" id="' + elem.id + '" data-beneficiaryId="' + elem.beneficiary_id + '">-</div>';
          }
          _html += '   <div class="distbursement-content">';
          _html += '   <div class="d-name">' + elem.beneficiary_name + "</div>";
          _html += '   <div class="d-date">' + tcg.ac.formatDateFull(elem.create_date, "yyyy-MM-dd") + "</div>";
          _html += '   <div class="d-Tbeating">' + elem.team_bet + "</div>";
          _html += '   <div class="d-TPNL">' + elem.team_profit + "</div>";
          _html += '   <div class="d-active-member">' + elem.active_member + "</div>";
          _html += '   <div class="d-ratio">' + Math.round(elem.contract_rate * 100, 2) + "%</div>";
          _html += '   <div class="d-amount">' + elem.amount + "</div>";
          _html += action;
          _html += "   </div>";
        }
      } else {
        _html += "<div class='tableContent-wrp'><div class='noResult-data'>" + tcg.lib.prop("no_result") + "</div>";
      }
    } else {
      _html += "<div class='tableContent-wrp'><div class='noResult-data'>" + tcg.lib.prop("no_result") + "</div>";
    }

    $("#tblDisbursementRecords").html(_html);
  };

  tcg.ac.ui.loadContractManagementList = function (result, agentDownlines, search) {
    var list = result.downline;
    var adList = [];
    var _list = "";

    $("#usedContracts").text(result.footer.usedContracts);
    $("#totalContracts").text(result.footer.totalContracts);

    for (var i = 0; i < list.length; i++) {
      adList.push(list[i].receiver);

      if (list[i].status != "C" && list[i].status != "R" && list[i].status != "V") {
        _list += "<div class='d-cont-wrp'>";
        _list += "<div class='d-name'>" + list[i].receiver + "</div>";
        _list +=
          "<div class='d-contact'><a href='javascript:void(0);' data-targetCustomer='" +
          list[i].receiver +
          "' data-requestedRate='" +
          list[i].contractRate +
          "' data-mode='view' class='action-btn viewList'>" +
          tcg.lib.prop("label_details") +
          "</a></div>";
        _list += "<div class='d-time'>" + tcg.ac.timeToDateFormat(list[i].createdDate, "dateTime") + "</div>";
        var approveDate =
          list[i].processedDate != null ? tcg.ac.timeToDateFormat(list[i].processedDate, "dateTime") : "-";
        _list += "<div class='d-apTime'>" + approveDate + "</div>";
        var status;
        var action;
        switch (list[i].status) {
          case "P":
            status = tcg.lib.prop("ac_status_P");
            action = tcg.lib.prop("ac_waiting_to_determined");
            break;
          case "A":
            status = tcg.lib.prop("ac_status_A");
            action =
              "<a href='javascript:void(0);' data-contractRate='" +
              list[i].contractRate +
              "' data-requestId='" +
              list[i].id +
              "' data-targetCustomer='" +
              list[i].receiver +
              "' class='action-btn void pointer'>" +
              tcg.lib.prop("ac_termination") +
              "</a>  |   <a href='javascript:void(0);' data-contractRate='" +
              list[i].contractRate +
              "' data-requestId='" +
              list[i].id +
              "' data-targetCustomer='" +
              list[i].receiver +
              "' data-contractRate='" +
              list[i].contractRate +
              "' data-minimumBet='" +
              list[i].minimumBet +
              "' data-activeMember='" +
              list[i].activeMember +
              "' data-mode='edit' class='action-btn viewList'>" +
              tcg.lib.prop("ac_modify") +
              "</a>";
            break;
          case "E":
            status = tcg.lib.prop("ac_status_P");
            action = tcg.lib.prop("ac_waiting_to_determined");
            break;
          case "C":
            status = "Cancelled";
            action =
              "<a href='javascript:void(0);' data-targetCustomer='" +
              list[i].receiver +
              "' data-requestedRate='" +
              list[i].contractRate +
              "' class='action-btn request'>" +
              tcg.lib.prop("ac_initiate_contract") +
              "</a>";
            break;
          case "R":
            status = "Rejected";
            action =
              "<a href='javascript:void(0);' data-targetCustomer='" +
              list[i].receiver +
              "' data-requestedRate='" +
              list[i].contractRate +
              "' class='action-btn request'>" +
              tcg.lib.prop("ac_initiate_contract") +
              "</a>";
            break;
          default:
            status = tcg.lib.prop("ac_status_DEF");
            action =
              "<a href='javascript:void(0);' data-targetCustomer='" +
              list[i].receiver +
              "' data-requestedRate='" +
              list[i].contractRate +
              "' class='action-btn request'>" +
              tcg.lib.prop("ac_initiate_contract") +
              "</a>";
        }
        _list += "<div class='d-status'>" + status + "</div>";
        _list += "<div class='d-histLog'>-</div>";
        _list += "<div class='d-action'>" + action + "</div>";
        _list += "</div>";
      }
    }

    if (!search) {
      for (var i = 0; i < agentDownlines.length; i++) {
        if (_list.indexOf(agentDownlines[i].customerName) < 0) {
          _list += "<div class='d-cont-wrp'>";
          _list += "<div class='d-name'>" + agentDownlines[i].customerName + "</div>";
          _list += "<div class='d-contact'>-</div>";
          _list += "<div class='d-time'>-</div>";
          var approveDate =
            agentDownlines[i].processedDate != null
              ? tcg.ac.timeToDateFormat(agentDownlines[i].processedDate, "dateTime")
              : "-";
          _list += "<div class='d-apTime'>" + approveDate + "</div>";
          var status;
          var action;
          if (agentDownlines[i].status == "P") {
            status = tcg.lib.prop("ac_status_P");
            action = tcg.lib.prop("ac_waiting_to_determined");
          } else if (agentDownlines[i].status == "A" || agentDownlines[i].status == "E") {
            status = tcg.lib.prop("ac_status_A");
            action =
              "<a href='javascript:void(0);' data-contractRate='" +
              agentDownlines[i].contractRate +
              "' data-requestId='" +
              agentDownlines[i].id +
              "' class='action-btn void pointer'>" +
              tcg.lib.prop("ac_termination") +
              "</a>  |   <a href='javascript:void(0);' data-contractRate='" +
              agentDownlines[i].contractRate +
              "' data-targetCustomer='" +
              agentDownlines[i].receiver +
              "' data-contractRate='" +
              agentDownlines[i].contractRate +
              "' data-minimumBet='" +
              agentDownlines[i].minimumBet +
              "' data-activeMember='" +
              agentDownlines[i].activeMember +
              "' class='action-btn modify'>" +
              tcg.lib.prop("ac_modify") +
              "</a>";
          } else {
            status = tcg.lib.prop("ac_status_DEF");
            action =
              "<a href='javascript:void(0);' data-targetCustomer='" +
              agentDownlines[i].customerName +
              "' data-requestedRate='" +
              agentDownlines[i].contractRate +
              "' class='action-btn request'>" +
              tcg.lib.prop("ac_initiate_contract") +
              "</a>";
          }
          _list += "<div class='d-status'>" + status + "</div>";
          _list += "<div class='d-histLog'>-</div>";
          _list += "<div class='d-action'>" + action + "</div>";
          _list += "</div>";
        }
      }
    } else {
      for (var i = 0; i < agentDownlines.length; i++) {
        if (agentDownlines[i].customerName == search) {
          if (_list.indexOf(search) < 0) {
            _list += "<div class='d-cont-wrp'>";
            _list += "<div class='d-name'>" + search + "</div>";
            _list += "<div class='d-contact'>-</div>";
            _list += "<div class='d-time'>-</div>";
            _list += "<div class='d-apTime'>-</div>";
            var status;
            var action;
            status = tcg.lib.prop("ac_status_DEF");
            action =
              "<a href='javascript:void(0);' data-targetCustomer='" +
              search +
              "' data-requestedRate='' class='action-btn request'>" +
              tcg.lib.prop("ac_initiate_contract") +
              "</a>";
            _list += "<div class='d-status'>" + status + "</div>";
            _list += "<div class='d-histLog'>-</div>";
            _list += "<div class='d-action'>" + action + "</div>";
            _list += "</div>";
          }
        }
      }
    }

    $("#contractManagementList").html(_list);
    tcg.ac.ui.sendContractRequest();
    tcg.ac.voidContract();
    tcg.ac.modifyContract();
    tcg.ac.ui.backToContractManagement();
  };

  tcg.ac.ui.loadContractRequest = function (result) {
    var _html = "";
    _html += '<div id="contract_request_modal" class="hag_modal">';
    _html += '    <div class="hag_modal_main">';
    _html += '    <div class="hag_modal_body">';
    _html += '<div class="receiver-dvdnt" id="">';
    _html += '<div class="receiver-dvdnt-wrp">';
    _html += '<div class="inline-block top-contner">';
    _html += "</div>";
    _html += '<div class="reciever-title-header">';
    if (tcg.ac.getMerchant() == "hongda") {
      _html += '<div class="po-middle new r-betting">周期消费</div>';
      _html += '<div class="po-middle new r-betVolume">周期亏损</div>';
      _html += '<div class="po-middle new r-amember">活跃人数</div>';
      _html += '<div class="po-middle new r-drate">分红比例</div>';
    } else {
      _html += '<div class="r-betVolume po-middle">' + tcg.lib.prop("ac_dividend_requirements") + "</div>";
      _html += '<div class="r-amember po-middle">' + tcg.lib.prop("label_active_number") + "</div>";
      _html += '<div class="r-drate po-middle">' + tcg.lib.prop("label_dividend_ratio") + "</div>";
    }
    _html += "</div>";
    _html += '<div class="reciever-content-wrp" id="contractList">';
    _html += '<div class="reciever-content">';
    _html += "</div>";
    var new_result = tcg.ac.dividendChangeListVlue(result.value);
    for (var i = 0; i < new_result.length; i++) {
      var elem = new_result[i],
        minimumBetCol = "",
        widthClass = "";
      if (tcg.ac.getMerchant() == "hongda") {
        minimumBetCol = '<div class="po-middle">' + elem.minimumBet + tcg.lib.prop("ac_yuan") + "</div>";
        widthClass = "new";
      }
      _html +=
        '<div class="reciever-content">' +
        minimumBetCol +
        '<div class="po-middle ' +
        widthClass +
        '">' +
        elem.netLoss +
        tcg.lib.prop("ac_yuan") +
        '</div><div class="po-middle ' +
        widthClass +
        '">' +
        elem.activeMember +
        tcg.lib.prop("label_unit_people") +
        '</div><div class="po-middle ' +
        widthClass +
        '">' +
        parseInt(elem.contractRate * 100) +
        "%</div></div>";
    }

    _html += "</div>";
    _html += '<div class="reciever-guide">';
    _html += '<div class="inline-block top-contner">';
    _html += "</div>";
    _html += '<div class="g-list-r"><span>' + tcg.lib.prop("ac_receiver_guide_1") + "</span>";
    _html += "<ol>";
    _html += "<li>" + tcg.lib.prop("ac_receiver_guide_2") + "</li>";
    _html += "<li>" + tcg.lib.prop("ac_receiver_guide_3") + "</li>";
    _html += "<li>" + tcg.lib.prop("ac_receiver_guide_4") + "</li>";
    _html += "</ol>";
    _html += "</div>";
    _html += '<div class="footer-con">';
    _html +=
      '<div class="red-bnt po-middle" id="approveContract"><input value="' +
      tcg.lib.prop("ac_datepicker_closeText") +
      '" class=" form-submit" type="button"></div>';
    _html +=
      '<div class="cancel_req po-middle" id="rejectContract"><input value="' +
      tcg.lib.prop("ac_cancel") +
      '" class=" ch-overyd" type="button"></div>';
    _html += "</div>";
    _html += "</div>";
    _html += "</div>";
    _html += "</div>";
    _html += "</div>";
    _html += "</div>";
    _html += "</div>";
    $("body").append(_html);
  };
  tcg.ac.ui.loadContractRequestCopy = function (result) {
    globalVar.contractRequests = result;
    var _html = "";
    _html += '<div id="contract_request_modal" class="hag_modal">';
    _html += '    <div class="hag_modal_main">';
    _html += '    <div class="hag_modal_body">';
    _html += '<div class="receiver-dvdnt" id="">';
    _html += '<div class="receiver-dvdnt-wrp">';
    _html += '<div class="inline-block top-contner">';
    _html += '<div class="label-name po-middle">产品</div>';
    _html += '<div class="slect-box-custom mem-icon2 po-middle red-up">';
    _html += '<select class="form-control ctSelect" name="gameType" data-placeholder="&nbsp;">';
    if (sessionStorage.merchantCode == "xinying") {
      _html += '<option value="CONS">综合</option>';
    } else {
      _html += '<option value="CONS">综合</option>';
      _html += '<option value="LOTT">彩票</option>';
    }
    _html += "</select></div>";
    //_html += '<div class="label-name po-middle" style="color: red;">请先选择产品</div>';
    _html += "</div>";
    _html += '<div class="reciever-title-header">';
    _html += '<div class="po-middle new r-betting">周期消费</div>';
    _html += '<div class="po-middle new r-betVolume">周期亏损</div>';
    _html += '<div class="po-middle new r-amember">活跃人数</div>';
    _html += '<div class="po-middle new r-drate">分红比例</div>';
    _html += "</div>";
    _html += '<div class="reciever-content-wrp" id="contractList">';
    _html += '<div class="reciever-content">';
    _html += "</div>";
    var new_result = tcg.ac.dividendChangeListVlue(result.value);
    for (var i = 0; i < new_result.length; i++) {
      if (new_result[i].gameType == "CONS") {
        var elem = new_result[i],
          minimumBetCol = "",
          widthClass = "";
        _html +=
          '<div class="reciever-content"><div class="po-middle new">' +
          elem.minimumBet +
          "元" +
          '</div><div class="po-middle new">' +
          elem.netLoss +
          "元" +
          '</div><div class="po-middle new">' +
          elem.activeMember +
          "人" +
          '</div><div class="po-middle new">' +
          parseInt(elem.contractRate * 100) +
          "%</div></div>";
      }
    }

    _html += "</div>";
    _html += '<div class="reciever-guide">';
    _html += '<div class="inline-block top-contner">';
    _html += "</div>";
    _html +=
      '<div class="g-list-r"><span>为保障代理的分红利益，平台推出“契约分红协议”，该协议由平台上下级代理以自愿的原则签署，并接受平台的监督。</span>';
    _html += "<ol>";
    _html += "<li>“契约协议” 就是上级与下级在平台内部建立一种分红约定。</li>";
    _html += "<li>如在当期结束时间后，仍未发放分红，平台将认定为拒绝发放，系统将冻结上级账号并强制发放给下级。</li>";
    _html += "<li>平台的所有规则与条款，平台保留最终解释权！</li>";
    _html += "</ol>";
    _html += "</div>";
    _html += '<div class="footer-con">';
    _html +=
      '<div class="red-bnt po-middle" id="approveContract"><input value="同意" class=" form-submit" type="button"></div>';
    _html +=
      '<div class="cancel_req po-middle" id="rejectContract"><input value="拒绝" class=" ch-overyd" type="button"></div>';
    _html += "</div>";
    _html += "</div>";
    _html += "</div>";
    _html += "</div>";
    _html += "</div>";
    _html += "</div>";
    _html += "</div>";
    $("body").append(_html);
    tcg.ac.ui.customSelect("#contract_request_modal select[name='gameType']");
    $('#contract_request_modal select[name="gameType"]').on("change", function (e) {
      $("#contractList").html("");
      var optionSelected = $("option:selected", this);
      var valueSelected = this.value;
      var new_result = tcg.ac.dividendChangeListVlue(globalVar.contractRequests.value);
      var _list = "";
      for (var i = 0; i < new_result.length; i++) {
        if (new_result[i].gameType == valueSelected) {
          var elem = new_result[i],
            minimumBetCol = "",
            widthClass = "";
          _list +=
            '<div class="reciever-content"><div class="po-middle new">' +
            elem.minimumBet +
            "元" +
            '</div><div class="po-middle new">' +
            elem.netLoss +
            "元" +
            '</div><div class="po-middle new">' +
            elem.activeMember +
            "人" +
            '</div><div class="po-middle new">' +
            parseInt(elem.contractRate * 100) +
            "%</div></div>";
        }
      }
      $("#contractList").html(_list);
    });
  };

  //tcg.ac.ui.loadContractRequest = function (result) {
  //    var _html = '';
  //    _html += '<div id="contract_request_modal" class="hag_modal">';
  //    _html += '    <div class="hag_modal_main">';
  //    _html += '    <div class="hag_modal_body">';
  //    _html += '    <div id="viewDLContractList" class="hag_modal_body hide">';
  //    _html += '    <div class="hag_bonus_dirmtitle hag_modal_btitle">我的分红规则';
  //    _html += '    <span class="hag_modal_bclose"></span>';
  //    _html += '    </div>';
  //    _html += '    <div class="hag_modal_bilist">';
  //    _html += '    </div>';
  //    _html += '    </div>';
  //    _html += '    <div class="hag_modal_bwrite hag_alluse_scroll">';
  //    _html += '<ul class="hag_bouns_dirlul">';
  //    _html += '<li class="hag_bouns_dirlulf"><div class="hag_bouns_w310">分红要求：半月总量</div>    <div class="hag_bouns_w310">活跃人数</div><div class="hag_bouns_w310">分红比例</div></li>';
  //    for (var i = 0; i < result.value.length; i++) {
  //        var elem = result.value[i];
  //        _html += '<li><div class="hag_bouns_w310">半月≥' + elem.minimumBet + '元</div><div class="hag_bouns_w310">' + elem.activeMember + '人</div><div class="hag_bouns_w310">' + (elem.contractRate * 100) + '%</div></li>';
  //    }
  //    _html += '</ul>';
  //    _html += '<div class="c-r-instruc-cont"><ol>';
  //    _html += '<li>协议双发认可该协议所达成的分红规则。</li>';
  //    _html += '<li>每半个月为一个分红周期，不累计。</li>';
  //    _html += '<li>每个月的1号和16号为分红发放时间，分红金额按照亏损金额对应分红比例进行派发。</li>';
  //    _html += '<li>您的上级余额已被暂时冻结，优先下级分红领取。</li>';
  //    _html += '<li>无论发生任何情况上级必须优先发放下级的应得分红，否则平台将强制执行发放分红。</li>';
  //    _html += '<li>此协议分红仅对协议双方有效。</li>';
  //    _html += '<li>平台仅作为第三方监督机构来确保本协议的有效性。</li>';
  //    _html += '<li>彩享有最终解释和修改权。</li>';
  //    _html += '</ol></div>';
  //    _html += '<div class="hag_modal_srbtn c-r-width">';
  //    _html += '    <div id="approveContract" class="hag_bouns_dirltprbt hag_bouns_dirltprbs3">';
  //    _html += '    <span>接受</span>';
  //    _html += '    </div>';
  //    _html += '    <div id="rejectContract" class="hag_cancel hag_bouns_dirltprbt hag_bouns_dirltprbs4">';
  //    _html += '<span>拒绝</span></div>';
  //    _html += '</div>';
  //    _html += '</div>';
  //    _html += '</div>';
  //    _html += '</div>';
  //    _html += '</div>';
  //    _html += '</div>';
  //    $("body").append(_html);
  //};
  tcg.ac.ui.sendContractRequest = function () {
    $("#contractManagementList .request")
      .unbind("click")
      .bind("click", function () {
        $("#contractManagement").addClass("hide");
        $("#editContract").removeClass("hide");
        $(".add-contract").removeClass("hide");
        var targetCustomer = $(this).attr("data-targetCustomer");
        $(".nameHolder-div span.po-middle").html(targetCustomer);
        $(".contractList").html("");

        tcg.ac.addContractItem();
      });
  };
  tcg.ac.ui.validateContractInput = function () {
    var form = $(".add-contract"),
      betVolume = form.find("#cm_betVolume"),
      actMember = form.find("#cm_actMember"),
      rate = form.find("#cm_rate");

    $(document)
      .off("keypress", ".add-contract div input")
      .on("keypress", ".add-contract div input", function (e) {
        if (e.keyCode == 13) {
          $(".add-contract .add-action").trigger("click");
          e.preventDefault();
        }
      });
    $(document)
      .off("keyup", ".add-contract div input")
      .on("keyup", ".add-contract div input", function (e) {
        var extractedNumber = tcg.ac.extractNumber(this.value);
      });
  };
  tcg.ac.ui.loadContractManagementListDownlines = function (result, mode) {
    var _contractItem = "";
    var downline = result.result.downline;
    var action;
    if (mode == "view") {
      action = '<div class="d-action">-</div>';
    } else {
      action = '<div class="d-action action-btn">' + tcg.lib.prop("ac_delete_btn") + "</div>";
    }
    var i,
      id = 1;
    for (i = 0; i < downline.length; i++) {
      if (downline[i].status != "C" && downline[i].status != "R" && downline[i].status != "V") {
        _contractItem += '<div class="divident-content-wrp">';
        _contractItem += '<div class="d-rnumber" data-requestId="' + downline[i].id + '">' + id + "</div>";
        _contractItem += '<div class="d-betVolume">' + downline[i].minimumBet + "</div>";
        _contractItem += '<div class="d-act-member">' + downline[i].activeMember + "</div>";
        _contractItem +=
          '<div class="d-rate">' + tcg.ac.percentRate(downline[i].contractRate, "rateToPercent") + "</div>";
        _contractItem += action;
        _contractItem += "</div>";
        id++;
      }
    }
    $(".contractList").append(_contractItem);
    $(".add-contract .d-rnumber").text(id);
    if (mode != "view") {
      $(".add-contract").removeClass("hide");
    }
  };
  tcg.ac.ui.backToContractManagement = function () {
    $(document)
      .off("click", ".backToContractManagement")
      .on("click", ".backToContractManagement", function () {
        $("#contractManagementList").html("");
        $("#contractManagementForm .form-submit").click();
        $("#editContract").addClass("hide");
        $("#contractManagement").removeClass("hide");
      });
  };
  tcg.ac.ui.loadDisbursementList = function (result) {
    var list = result.list,
      _disbursementItem = "";
    $(".distbursement-content").remove();
    for (var i = 0; i < list.length; i++) {
      _disbursementItem += '<div class="distbursement-content">';
      _disbursementItem += '<div class="d-name">' + list[i].beneficiary_name + "</div>";
      _disbursementItem += '<div class="d-date">' + list[i].create_date + "</div>";
      _disbursementItem += '<div class="d-Tbeating">' + list[i].team_bet + "</div>";
      _disbursementItem += '<div class="d-TPNL">' + list[i].team_profit + "</div>";
      _disbursementItem += '<div class="d-active-member">' + list[i].active_member + "</div>";
      _disbursementItem +=
        '<div class="d-ratio">' + tcg.ac.percentRate(list[i].contract_rate, "rateToPercent") + "</div>";
      _disbursementItem += '<div class="d-amount">' + list[i].amount + "</div>";
      _disbursementItem +=
        '<div class="action-btn disburse" id="' +
        list[i].id +
        '" data-beneficiaryId="' +
        list[i].beneficiary_id +
        '">派发</div></div>';
    }
    $("#disbursementList").append(_disbursementItem);
  };

  /******** agentDailyWages 日工资 ********/

  tcg.ac.ui.dailySalDisbursementRecords = function (rs) {
    var _html =
      '<li class="hag_bouns_dirlulf"><div class="hag_bouns_w150">' +
      tcg.lib.prop("ac_subordination_username") +
      '</div><div class="hag_bouns_w150">' +
      tcg.lib.prop("ac_settlement_time") +
      '</div><div class="hag_bouns_w150">' +
      tcg.lib.prop("ac_lotto_pnl_summaryType_1_column_4") +
      '</div><div class="hag_bouns_w150">' +
      tcg.lib.prop("ac_active_member_no") +
      '</div><div class="hag_bouns_w200">' +
      tcg.lib.prop("ac_daily_wage_ratio") +
      '</div><div class="hag_bouns_w200">' +
      tcg.lib.prop("ac_amount_of_wages") +
      "</div></li>";

    if (rs.length > 0) {
      for (var i = 0; i < rs.length; i++) {
        _html += "<li>";
        _html += '<div class="hag_bouns_w150">' + rs[i].beneficiary_unclude_merchant + "</div>";
        _html += '<div class="hag_bouns_w150">' + rs[i].create_time + "</div>";
        _html += '<div class="hag_bouns_w150">' + rs[i].team_betting + "</div>";
        _html += '<div class="hag_bouns_w150">' + rs[i].active_member + "</div>";
        if (rs[i].rate_type == "F") {
          _html += '<div class="hag_bouns_w200">' + tcg.lib.prop("ac_fixed_amount") + rs[i].rate + "</div>";
        } else {
          _html += '<div class="hag_bouns_w200">' + tcg.lib.prop("ac_proportion") + rs[i].rate * 100 + "%</div>";
        }
        _html += '<div class="hag_bouns_w200">' + rs[i].amount + "</div>";
        _html += "</li>";
      }
    } else {
      _html += "<div class='tableContent-wrp'>";
      _html += "<div class='noResult-data'>" + tcg.lib.prop("no_result") + "</div>";
      _html += "</div>";
    }

    $("#dailySalDisbursementRList").html(_html);
  };
  tcg.ac.ui.loadSalaryDownlines = function (result, downlineContracts) {
    //  Declare
    var _html = "",
      registerAmount = 0;
    var status = {
      P: { status: tcg.lib.prop("ac_status_P") },
      A: { status: tcg.lib.prop("ac_status_A") },
      E: { status: tcg.lib.prop("ac_status_E") },
      C: { status: tcg.lib.prop("ac_status_C") },
      V: { status: tcg.lib.prop("ac_status_V") },
      R: { status: tcg.lib.prop("ac_status_R") },
      DEF: { status: tcg.lib.prop("ac_status_DEF") },
    };

    //  Show result
    var downline_contracts = {};
    if (downlineContracts.value.downline) {
      var downlineContracts = downlineContracts.value.downline;
      $(downlineContracts).map(function () {
        downline_contracts[this.receiverId] = this;
      });
    }
    _html += '    <li class="hag_bouns_dirlulf">';
    _html +=
      '    <div class="hag_bouns_name">' +
      tcg.lib.prop("ac_subordinate_management_register_downline_account_placeholder") +
      "</div>";
    _html += '    <div class="hag_bouns_w100">' + tcg.lib.prop("ac_agreement_content") + "</div>";
    _html += '    <div class="hag_bouns_w200">' + tcg.lib.prop("ac_signing_time") + "</div>";
    _html += '    <div class="hag_bouns_w200">' + tcg.lib.prop("label_agreement_effective_time") + "</div>";
    _html += '    <div class="hag_bouns_w100">' + tcg.lib.prop("label_status") + "</div>";
    _html += '    <div class="hag_bouns_w100">' + tcg.lib.prop("label_history_of_sign_contract") + "</div>";
    _html +=
      '    <div class="hag_bouns_dirulicfr hag_bouns_w100 added-marg">' +
      tcg.lib.prop("ac_link_manager_column_8") +
      "</div>";
    _html += "    </li>";

    try {
      if (result.length) {
        var status_mem = "DEF";
        for (var i = 0; i < result.length; i++) {
          var entry = result[i];
          var createdDate = "-";
          var processed_date = "-";
          var log_action = "-";
          var add_contract_btn = "";
          add_contract_btn +=
            '<div class="hag_bouns_dirulicfr add_salary hag_bouns_w100" data-targetCustomer="' +
            entry.customer_id +
            '">';
          add_contract_btn +=
            '<span class="hag_bouns_dirulibtn pointer action-btn" >' + tcg.lib.prop("ac_initiate_contract") + "</span>";
          add_contract_btn += "</div>";
          var dividend_info = '<div class="hag_bouns_w100 hag_bouns_c4c" >' + "-" + "</div>";
          if (downline_contracts[entry.customer_id]) {
            status_mem = downline_contracts[entry.customer_id].status;
            if (downline_contracts[entry.customer_id].processedDate) {
              processed_date = tcg.ac.formatDateFull(
                downline_contracts[entry.customer_id].processedDate * 1,
                "yyyy-MM-dd hh:mm"
              );
            }
            if (downline_contracts[entry.customer_id].createdDate) {
              createdDate = tcg.ac.formatDateFull(
                downline_contracts[entry.customer_id].createdDate * 1,
                "yyyy-MM-dd hh:mm"
              );
            }
            log_action =
              "<span class='salary_log hag_bouns_c4c pointer action-btn' data-customer_id='" +
              entry.customer_id +
              "'>" +
              tcg.lib.prop("ac_signing_history") +
              "</span>";
            dividend_info =
              '<div class="hag_bouns_w100 hag_bouns_c4c viewSalaryDetails action-btn pointer" data-request_id="' +
              downline_contracts[entry.customer_id].id +
              '" data-targetCustomer="' +
              entry.customer_name +
              '" data-requestedRate="' +
              downline_contracts[entry.customer_id].contractRate +
              '" >' +
              tcg.lib.prop("label_details") +
              "</div>";
            if (downline_contracts[entry.customer_id].status == "A") {
              add_contract_btn = "";
              add_contract_btn += '<div class="hag_bouns_dirulicfr hag_bouns_c4c hag_bouns_w100">';
              add_contract_btn +=
                '<span class="pointer void_salary action-btn" data-targetCustomer="' +
                entry.customer_name +
                '"  data-request_id="' +
                downline_contracts[entry.customer_id].id +
                '" >' +
                tcg.lib.prop("ac_termination") +
                "</span> | ";
              add_contract_btn +=
                '<span class="pointer edit_salary action-btn" data-targetCustomer="' +
                entry.customer_id +
                '" data-request_id="' +
                downline_contracts[entry.customer_id].id +
                '" >' +
                tcg.lib.prop("ac_modify") +
                "</span>";
              add_contract_btn += "</div>";
            } else if (
              downline_contracts[entry.customer_id].status == "P" ||
              downline_contracts[entry.customer_id].status == "V"
            ) {
              add_contract_btn = "";
              add_contract_btn += '<div class="hag_bouns_dirulicfr">';
              add_contract_btn += tcg.lib.prop("ac_waiting_to_determined");
              add_contract_btn += "</div>";
            } else if (downline_contracts[entry.customer_id].status == "E") {
              add_contract_btn = "";
              add_contract_btn += '<div class="hag_bouns_dirulicfr">';
              add_contract_btn += tcg.lib.prop("ac_waiting_to_determined");
              add_contract_btn += "</div>";
            }
          }
          _html += "<li>";
          _html += '<div class="hag_bouns_name">' + entry.customer_name + "</div>";
          _html += dividend_info;
          _html += '<div class="hag_bouns_w200">' + createdDate + "</div>";
          _html += '<div class="hag_bouns_w200">' + processed_date + "</div>";
          if (downline_contracts[entry.customer_id]) {
            _html += '<div class="hag_bouns_w100">' + status[status_mem].status + "</div>";
          } else {
            _html += '<div class="hag_bouns_w100">' + tcg.lib.prop("ac_status_DEF") + "</div>";
          }

          _html += '<div class="hag_bouns_w100 hag_bouns_c4c">' + log_action + "</div>";
          _html += add_contract_btn;
          _html += "</li>";
        }
      } else {
        _html += "<div class='tableContent-wrp'><div class='noResult-data'>" + tcg.lib.prop("no_result") + "</div>";
      }
    } catch (e) {
      _html += "<div class='tableContent-wrp'><div class='noResult-data'>" + tcg.lib.prop("no_result") + "</div>";
    }
    $("#downlineList").html(_html);
    $("#totalEffectiveAmount").text(registerAmount);
  };
  tcg.ac.ui.addNewSalRow = function (data) {
    var curr_inc = $("#addDailySalaryForm .rec_increment").html() * 1;
    curr_inc++;
    var _html = "";
    var rate_type = {
      P: "比例",
      F: "固定金额",
    };
    var dataRate = data.rate,
      sign = "%",
      rate;
    if (data.rateType == "F") {
      dataRate = Decimal(data.rate / data.betVolume)
        .times(100)
        .toString();
      sign = "";
      rate = data.rate;
    }
    if (data.rateType == "P") {
      if (Decimal(data.rate).decimalPlaces() === 0) {
        rate = Decimal(data.rate).toFixed(2).toString();
      } else {
        rate = tcg.ac.trimDecimal(data.rate, 2);
      }
    }
    // data.rateType = "P";
    _html += '<li class="sal_row row_has_val" id="con_row_index_' + (curr_inc - 1) + '">';
    _html += '<div class="hag_bouns_w150 hag_bouns_c4c">' + (curr_inc - 1) + "</div>";
    _html += '<div class="hag_bouns_w150 betVolume">' + data.betVolume + "</div>";
    _html += '<div class="hag_bouns_w150 actMembers">' + data.activeMembers + "</div>";
    _html +=
      '<div class="hag_bouns_w150 rateType" data-rateType="' +
      data.rateType +
      '">' +
      rate_type[data.rateType] +
      "</div>";
    _html += '<div class="hag_bouns_w150 rate" data-rate="' + dataRate + '">' + rate + sign + "</div>";
    $(".delete_sal_row").html("-").removeClass("hag_bouns_cf6");
    _html +=
      '<div class="hag_bouns_w150 hag_bouns_cf6 delete_sal_row"><span id="delete_sal_row" class="action-btn pointer">' +
      tcg.lib.prop("ac_delete_btn") +
      "</span></div>";
    _html += "</li>";
    $("#addDailySalaryForm .rec_increment").html(curr_inc);
    $("#addDailySalaryForm").before(_html);
  };
  tcg.ac.ui.loadMydailyList = function (res, type) {
    var data;
    if (type == "topUser") {
      try {
        data = res.value.length == 0 ? [] : res.value;
      } catch (err) {
        tcg.lib.alert("errors", "功能未开放，请联系客服。");
        return;
      }
    } else {
      try {
        data = res.value.agent.length == 0 ? [] : res.value.agent[0].details;
      } catch (err) {
        tcg.lib.alert("errors", "功能未开放，请联系客服。");
        return;
      }
    }
    var html = "";
    for (var i = 0; i < data.length; i++) {
      var multiplier = 100;
      var rate;
      var type = tcg.lib.prop("ac_proportion");
      if (data[i].rateType == "F") {
        rate = data[i].rate;
        type = tcg.lib.prop("ac_fixed_amount");
      } else {
        rate = tcg.ac.dailyPercentRate(data[i].rate, "rateToPercent", multiplier);
      }
      html +=
        '<ul data-minimumbet="' +
        data[i].betVolume +
        '" data-activemember="' +
        data[i].activeMember +
        '" data-rateType="' +
        data[i].rateType +
        '" data-contractrate="' +
        data[i].rate +
        '"><li>' +
        tcg.lib.prop("ac_day_volume") +
        "≥" +
        data[i].betVolume +
        tcg.lib.prop("ac_yuan") +
        "</li><li>" +
        data[i].activeMember +
        tcg.lib.prop("label_unit_people") +
        "</li><li>" +
        type +
        rate +
        "</li></ul>";
    }
    $(".js-my-daily-data").html(html);
  };
  tcg.ac.ui.editAddNewSalaryRow = function (data, isAutomated) {
    var curr_inc = $("#edit_salary_form .rec_increment").html() * 1;
    curr_inc++;
    var _html = "";
    var rate_type = {
      P: tcg.lib.prop("ac_proportion"),
      F: tcg.lib.prop("ac_fixed_amount"),
    };
    var dataRate = data.rate,
      sign = "%",
      rate;
    if (data.rateType == "F") {
      dataRate = Decimal(data.rate / data.betVolume)
        .times(100)
        .toString();
      sign = "";
      rate = data.rate;
    }
    if (data.rateType == "P") {
      if (Decimal(data.rate).decimalPlaces() === 0) {
        rate = Decimal(data.rate).toFixed(2).toString();
      } else {
        rate = tcg.ac.trimDecimal(data.rate, 2);
      }
    }
    // data.rateType = "P";
    _html +=
      '<li class="sal_row ' +
      (isAutomated ? "row_has_val" : "row_has_val") +
      '" id="con_row_index_' +
      (curr_inc - 1) +
      '">';
    _html += '<div class="hag_bouns_w150 hag_bouns_c4c">' + (curr_inc - 1) + "</div>";
    _html += '<div class="hag_bouns_w150 betVolume">' + data.betVolume + "</div>";
    _html += '<div class="hag_bouns_w150 actMembers">' + data.activeMember + "</div>";
    _html +=
      '<div class="hag_bouns_w150 rateType" data-rateType="' +
      data.rateType +
      '">' +
      rate_type[data.rateType] +
      "</div>";
    _html += '<div class="hag_bouns_w150 rate" data-rate="' + dataRate + '">' + rate + sign + "</div>";
    $(".delete_sal_row").html("-").removeClass("hag_bouns_cf6");
    _html +=
      '<div class="hag_bouns_w150 hag_bouns_cf6 delete_sal_row"><span id="delete_sal_row" class="action-btn pointer">' +
      tcg.lib.prop("ac_delete_btn") +
      "</span></div>";
    _html += "</li>";
    $("#edit_salary_form .rec_increment").html(curr_inc);
    $("#edit_salary_form").before(_html);
  };
  tcg.ac.ui.loadSalaryDetails = function (result) {
    var _html = "";
    _html += '<li class="hag_bouns_dirlulf">';
    _html += '    <div class="hag_bouns_w150">' + tcg.lib.prop("label_sort") + "</div>";
    _html += '    <div class="hag_bouns_w150">' + tcg.lib.prop("label_team_daily_amount") + "</div>";
    _html += '     <div class="hag_bouns_w150">' + tcg.lib.prop("label_active_requirement_for_sub") + "</div>";
    _html += '    <div class="hag_bouns_w150">' + tcg.lib.prop("label_type") + "</div>";
    _html += '   <div class="hag_bouns_w150">' + tcg.lib.prop("label_standard_of_daily_salary") + "</div>";
    // _html += '   <div class="hag_bouns_w150">' +tcg.lib.prop("label_operation")+ '</div>';
    // _html += '   <div class="hag_bouns_w150"></div>';
    _html += "   </li>";
    var rate_type = {
      P: tcg.lib.prop("ac_proportion"),
      F: tcg.lib.prop("ac_fixed_amount"),
    };
    if (result.value.details.length > 0) {
      for (var i = 0; i < result.value.details.length; i++) {
        var item = result.value.details[i];
        var rate;
        if (item.rateType == "F") {
          rate = item.rate;
        } else {
          rate = tcg.ac.percentRate(item.rate, "rateToPercentDS");
        }
        _html += "    <li>";
        _html += '    <div class="hag_bouns_w150 hag_bouns_c4c">' + (i + 1) + "</div>";
        _html += '   <div class="hag_bouns_w150">' + item.betVolume * 1 + "</div>";
        _html += '   <div class="hag_bouns_w150">' + item.activeMember * 1 + "</div>";
        _html += '   <div class="hag_bouns_w150">' + rate_type[item.rateType] + "</div>";
        _html += '   <div class="hag_bouns_w150">' + rate + "</div>";
        _html += '    <div class="hag_bouns_w150 hag_bouns_cf6"></div>';
        _html += "   </li>";
      }
    }

    $("#downlineSalaryDetailsList").html(_html);
  };
  tcg.ac.ui.loadSalaryHistoryLog = function (result) {
    var _html = "";
    // _html += '<div id="contract_history_modal" class="hag_modal">';
    _html += '    <div class="hag_modal_main">';
    _html += '    <div class="hag_modal_body">';
    _html += '  <div class="hag_bonus_dirmtitle hag_modal_btitle"><h3>' + tcg.lib.prop("ac_signing_history") + "</h3>";
    _html += '      <span class="hag_modal_bclose pointer"> X </span>';
    _html += "  </div>";
    _html += '    <div id="" class="hag_modal_body hide">';
    _html +=
      '    <div class="hag_bonus_dirmtitle hag_modal_btitle"><h3>' + tcg.lib.prop("ac_signing_history") + "</h3>";
    _html += '    <span class="hag_modal_bclose pointer"> X </span>';
    _html += "    </div>";
    _html += '    <div class="hag_modal_bilist">';
    _html += "    </div>";
    _html += "    </div>";
    _html += '    <div class="hag_modal_bwrite hag_alluse_scroll">';
    _html += '<ul class="hag_bouns_dirlul">';
    _html +=
      '<li class="hag_bouns_dirlulf"><div class="hag_bouns_w460">' +
      tcg.lib.prop("label_date") +
      '</div><div class="hag_bouns_w460">' +
      tcg.lib.prop("ac_history_record") +
      "</div></li>";
    for (var i = 0; i < result.value.length; i++) {
      var elem = result.value[i];
      var placeholder = "";
      switch (elem.action) {
        case "N":
          placeholder = tcg.lib.prop("ac_initiate_contract");
          break;
        case "E":
          placeholder = tcg.lib.prop("ac_modify_contract");
          break;
        case "C":
          placeholder = tcg.lib.prop("ac_termination_success");
          break;
        case "A":
          placeholder = tcg.lib.prop("ac_signed_success");
          break;
        case "R":
          placeholder = tcg.lib.prop("ac_termination");
          break;
        case "V":
          placeholder = tcg.lib.prop("ac_initiate_termination");
          break;
      }
      if (elem.action != "P") {
        _html +=
          '<li><div class="hag_bouns_w460">' +
          tcg.ac.timeToDateFormat(elem.activityDate, "dateTime") +
          '</div><div class="hag_bouns_w460">' +
          placeholder +
          "</div></li>";
      }
    }
    _html += "</ul>";
    _html += "</div>";
    _html += "</div>";
    _html += "</div>";
    _html += "</div>";
    _html += "</div>";
    // _html += '</div>';
    $("#contract_history_modal").append(_html);
    $("#contract_history_modal").removeClass("hide");
  };
  tcg.ac.ui.appendSalaryInfo = function (data) {
    for (var i = 0; i < data.length; i++) {
      $("#commission_container #one_container").append(
        "<p>" +
          tcg.lib.prop("ac_day_volume") +
          '≥<span id="">' +
          data[i].betVolume +
          "</span>" +
          tcg.lib.prop("ac_yuan") +
          "</p>"
      );
      $("#commission_container #two_container").append(
        '<p><span id="">' + data[i].activeMember + "</span>" + tcg.lib.prop("label_unit_people") + "</p>"
      );
      var multiplier = 100;
      var rate;
      var type = tcg.lib.prop("ac_proportion");
      if (data[i].rateType == "F") {
        multiplier = 1;
        rate = data[i].rate;
        type = tcg.lib.prop("ac_fixed_amount");
      } else {
        rate = tcg.ac.percentRate(data[i].rate, "rateToPercent", multiplier);
      }
      $("#commission_container #three_container").append('<p><span id="">' + type + rate + "</span></p>");
    }
  };

  tcg.ac.ui.loadSalaryRequest = function (result) {
    rs = result;
    result = result.value[0].details;

    var _html = "";
    _html += '<div id="contract_request_modal" class="hag_modal">';
    _html += '    <div class="hag_modal_main">';
    _html += '    <div class="hag_modal_body">';
    _html += '    <div id="viewDLContractList" class="hag_modal_body hide">';
    _html += '    <div class="hag_bonus_dirmtitle hag_modal_btitle">' + tcg.lib.prop("ac_modify_contract");
    _html += '    <span class="hag_modal_bclose"></span>';
    _html += "    </div>";
    _html += '    <div class="hag_modal_bilist">';
    _html += "    </div>";
    _html += "    </div>";
    _html += '    <div class="hag_modal_bwrite hag_alluse_scroll">';
    _html += '<ul class="hag_bouns_dirlul">';
    _html +=
      '<li class="hag_bouns_dirlulf"><div class="hag_bouns_w310">' +
      tcg.lib.prop("ac_salary_details_2") +
      '</div>    <div class="hag_bouns_w310">' +
      tcg.lib.prop("ac_salary_details_3") +
      '</div><div class="hag_bouns_w310">' +
      tcg.lib.prop("ac_salary_details_4") +
      "</div></li>";
    for (var i = 0; i < result.length; i++) {
      var elem = result[i];
      var rate;
      var type = tcg.lib.prop("ac_proportion");
      if (elem.rateType == "F") {
        rate = elem.rate;
        type = tcg.lib.prop("ac_fixed_amount");
      } else {
        rate = tcg.ac.percentRate(elem.rate, "rateToPercentDS");
      }
      _html +=
        '<li><div class="hag_bouns_w310">' +
        tcg.lib.prop("ac_day_volume") +
        "≥" +
        elem.betVolume +
        tcg.lib.prop("ac_yuan") +
        '</div><div class="hag_bouns_w310">' +
        elem.activeMember +
        '人</div><div class="hag_bouns_w310">' +
        type +
        rate +
        "</div></li>";
    }
    _html += "</ul>";
    _html += '<div class="c-r-instruc-cont"><ol>';
    _html += "<li>" + tcg.lib.prop("ac_daily_wage_stat_1") + "</li>";
    _html += "<li>" + tcg.lib.prop("ac_daily_wage_stat_2") + "</li>";
    _html += "<li>" + tcg.lib.prop("ac_daily_wage_stat_3") + "</li>";
    _html += "</ol></div>";
    _html += '<div class="hag_modal_srbtn c-r-width">';
    _html +=
      '    <div id="approveSalary" data-id="' + rs.value[0].id + '" class="hag_bouns_dirltprbt hag_bouns_dirltprbs3">';
    _html += "    <span>" + tcg.lib.prop("ac_accept") + "</span>";
    _html += "    </div>";
    _html +=
      '    <div id="rejectSalary"  data-id="' +
      rs.value[0].id +
      '" class="hag_cancel hag_bouns_dirltprbt hag_bouns_dirltprbs4">';
    _html += "<span>拒绝</span></div>";
    _html += "</div>";
    _html += "</div>";
    _html += "</div>";
    _html += "</div>";
    _html += "</div>";
    _html += "</div>";
    $("body").append(_html);
  };

  /******** agentDailyWages 日工资 END********/
  /******** AgentTeamPeopleCount 团队信息、AgentTeamContribution 团队分析、AgentProfitSharing 代理佣金 start********/

  tcg.ac.ui.loadagentTeamPeopleCount = function (rs) {
    var _column = "";
    _column += "<div>账号</div>";
    _column += "<div>团队人数</div>";
    _column += "<div>代理人数</div>";
    _column += "<div>会员人数</div>";
    _column += "<div>新增人数</div>";
    _column += "<div>投注人数</div>";
    _column += "<div>充值人数</div>";
    _column += "<div>提现人数</div>";
    $("#agentTeamPeopleCountListTitle").html(_column);
    var decimal = 2,
      _html = "";
    for (var i = 0; i < rs.length; i++) {
      _html += '<div class="agteam_icp_listmtit">';
      _html += "<div>" + rs[i].customer_name + "</div>";
      _html += "<div>" + rs[i].total_ppl + "</div>";
      _html += "<div>" + rs[i].agent_ppl + "</div>";
      _html += "<div>" + rs[i].member_ppl + "</div>";
      _html += "<div>" + rs[i].increased_ppl + "</div>";
      _html += "<div>" + rs[i].bet_ppl + "</div>";
      _html += "<div>" + rs[i].deposit_ppl + "</div>";
      _html += "<div>" + rs[i].withdraw_ppl + "</div>";
      _html += "</div>";
    }
    $("#agentTeamPeopleCountListMain").html(_html);
  };
  tcg.ac.ui.loadAgentTeamContribution = function (rs) {
    var _column = "";
    _column += "<div>用户名</div>";
    _column += "<div>充值</div>";
    _column += "<div>提款</div>";
    _column += "<div>棋牌净赢</div>";
    _column += "<div>棋牌净输</div>";
    _column += "<div>团队返点收入</div>";
    _column += "<div>团队贡献</div>";
    _column += "<div>棋牌盈利</div>";
    _column += "<div>真人投注</div>";
    _column += "<div>真人派奖</div>";
    _column += "<div>真人输赢</div>";
    _column += "<div>真人返点</div>";
    _column += "<div>电子投注</div>";
    _column += "<div>电子派奖</div>";
    _column += "<div>电子输赢</div>";
    _column += "<div>电子返点</div>";
    _column += "<div>娱乐红利</div>";
    _column += "<div>捕鱼投注</div>";
    _column += "<div>捕鱼派奖</div>";
    _column += "<div>捕鱼玩家返水</div>";
    _column += "<div>捕鱼代理返点</div>";
    _column += "<div>捕鱼盈亏</div>";
    _column += "<div>体育投注</div>";
    _column += "<div>体育派奖</div>";
    _column += "<div>体育玩家返水</div>";
    _column += "<div>体育代理返点</div>";
    _column += "<div>体育盈亏</div>";
    $("#agentTeamContributionListTitle").html(_column);
    var _html = "";
    for (var i = 0; i < rs.length; i++) {
      _html += '<div class="agteam_icp_listmtit">';
      _html += "<div>" + rs[i].agent_name + "</div>";
      _html += "<div>" + rs[i].deposit + "</div>";
      _html += "<div>" + rs[i].withdraw + "</div>";
      _html += "<div>" + rs[i].pvp_game_winnings + "</div>";
      _html += "<div>" + rs[i].pvp_game_loses + "</div>";
      _html += "<div>" + rs[i].pvp_commission + "</div>";
      _html += "<div>" + rs[i].pvp_commission_contribution + "</div>";
      _html += "<div>" + rs[i].pvp_game_pnl + "</div>";
      _html += "<div>" + rs[i].live_game_betting + "</div>";
      _html += "<div>" + rs[i].live_game_winning + "</div>";
      _html += "<div>" + rs[i].live_game_pnl + "</div>";
      _html += "<div>" + rs[i].live_game_rebate + "</div>";
      _html += "<div>" + rs[i].rng_game_betting + "</div>";
      _html += "<div>" + rs[i].rng_game_winning + "</div>";
      _html += "<div>" + rs[i].rng_game_pnl + "</div>";
      _html += "<div>" + rs[i].rng_game_rebate + "</div>";
      _html += "<div>" + rs[i].rng_promotion + "</div>";
      _html += "<div>" + rs[i].fish_game_betting + "</div>";
      _html += "<div>" + rs[i].fish_game_winning + "</div>";
      _html += "<div>" + rs[i].fish_game_rebate + "</div>";
      _html += "<div>" + rs[i].fish_agent_commission + "</div>";
      _html += "<div>" + rs[i].fish_game_pnl + "</div>";
      _html += "<div>" + rs[i].sports_game_betting + "</div>";
      _html += "<div>" + rs[i].sports_game_winning + "</div>";
      _html += "<div>" + rs[i].sports_game_rebate + "</div>";
      _html += "<div>" + rs[i].sports_agent_commission + "</div>";
      _html += "<div>" + rs[i].sports_game_pnl + "</div>";
      _html += "</div>";
    }
    $("#agentTeamContributionListMain").html(_html);
  };
  tcg.ac.ui.loadAgentProfitSharing = function (rs) {
    var _column = "";
    _column += "<div>日期</div>";
    _column += "<div>总投注</div>";
    _column += "<div>总派奖</div>";
    _column += "<div>红利费用</div>";
    _column += "<div>玩家反水</div>";
    _column += "<div>净盈利</div>";
    _column += "<div>负累计</div>";
    _column += "<div>最终盈亏</div>";
    _column += "<div>活跃人数</div>";
    _column += "<div>佣金比例</div>";
    _column += "<div>本月佣金</div>";
    $("#agentProfitSharingListTitle").html(_column);
    var _html = "";
    for (var i = 0; i < rs.length; i++) {
      _html += '<div class="agteam_icp_listmtit">';
      _html += "<div>" + rs[i].balance_date + "</div>";
      _html += "<div>" + rs[i].betting + "</div>";
      _html += "<div>" + rs[i].winning + "</div>";
      _html += "<div>" + rs[i].promotion + "</div>";
      _html += "<div>" + rs[i].player_rebate + "</div>";
      _html += "<div>" + rs[i].net_profit + "</div>";
      _html += "<div>" + rs[i].balance_brought_forward + "</div>";
      _html += "<div>" + rs[i].last_net_profit + "</div>";
      _html += "<div>" + rs[i].active_members + "</div>";
      _html += "<div>" + rs[i].rate + "</div>";
      _html += "<div>" + rs[i].actual_amount + "</div>";
      _html += "</div>";
    }
    $("#agentProfitSharingListMain").html(_html);
  };

  /******** AgentTeamPeopleCount 团队信息、AgentTeamContribution 团队分析、AgentProfitSharing 代理佣金 end********/
  // HTML Custom Popups and Confirmation Popups
  tcg.ac.ui.agentGenerateAffiliateUrlConfirm = function (data) {
    var TYPE = [tcg.lib.prop("label_member"), tcg.lib.prop("label_agent")];

    var qq =
      $.trim(data.qq) !== ""
        ? "<li><span>" + tcg.lib.prop("label_qq_promotion") + " : </span><span>" + $.trim(data.qq) + "</span></li>"
        : "";

    var _html = "";
    _html += '<div class="bet_content" style="max-height: 500px;overflow-y: auto;line-height: 1.5;">';
    _html += "    <p>" + tcg.lib.prop("ac_specific_info_confirm") + "</p>";
    _html += '   <ul class="" style="line-height: 1.5;text-align:center">';
    _html +=
      '   <li style="line-height: inherit; text-align: center;"><span style="line-height: inherit;">' +
      tcg.lib.prop("ac_bet_content_1") +
      " : </span><span>" +
      TYPE[data.type] +
      "</span></li>";
    _html +=
      '   <li style="line-height: inherit;width: 300px;word-break: break-all;display: inline-block;text-align: center;"><span style="line-height: inherit;">' +
      tcg.lib.prop("label_promotion_channel") +
      " : </span><span>" +
      data.path +
      "</span></li>";
    _html += qq;
    for (var i = 0; i < data.configs.length; i++) {
      var e = data.configs[i],
        playNameId = e.playNameId ? "-" + e.playNameId : "";
      prizeModeId = e.prizeModeId ? "_" + e.prizeModeId : "";
      _html +=
        '<li style="line-height: inherit;"><span style="line-height: inherit;">' +
        `${
          e.productType !== "5"
            ? `${tcg.ac.lotto_translate(e.gameCode + prizeModeId + playNameId)}`
            : `${
                e.gameCode === "ALLEXCLUDE" || e.gameCode === "SPORTS-PARLAY"
                  ? `${tcg.lib.prop(e.gameCode)}`
                  : `${tcg.lib.prop("SPORTS")} - ${e.gameCode}`
              }`
        }` +
        " : </span><span>" +
        e.rebate +
        "</span></li>";
    }
    _html += "</ul>";
    _html += "</div>";
    return _html;
  };
  tcg.ac.ui.linkManagerEnrollmentPopup = function (rs) {
    var _html = "";
    var entry = null;
    var list = "";
    //	Result
    for (var i = 0; i < rs.value.length; i++) {
      entry = rs.value[i];

      list += '<div class="dira-lm-enroll-trow clearfix">';
      list += '<div class="dira-lm-enroll-tcol">' + entry.customerName + "</div>";
      list += '<div class="dira-lm-enroll-tcol">' + tcg.ac.formatDateFull(entry.registeredDate) + "</div>";
      list += "</div>";
    }

    _html =
      "<div class='dira-lm-enroll x-pop'> \
				<h2>" +
      tcg.lib.prop("ac_link_manager_column_3") +
      "</h2> \
				<div class='dira-lm-enroll-thead clearfix'> \
					<div class='uname'>" +
      tcg.lib.prop("ac_subordinate_management_member_management_username_label") +
      "</div> \
					<div class='regDate'>" +
      tcg.lib.prop("ac_subordinate_management_member_management_column_4") +
      "</div> \
				</div> \
				<div class='dira-lm-enroll-tbody'>" +
      list +
      "</div> \
			</div>";

    return _html;
  };
  tcg.ac.ui.linkManagerAffiliateDetailPopup = function (entry) {
    //  Declare
    var _html = "";
    var list = "";
    //  Get entry
    var TYPE = [tcg.lib.prop("label_member"), tcg.lib.prop("label_agent")],
      STATUS = [
        tcg.lib.prop("ac_link_manager_status_option_4"),
        tcg.lib.prop("ac_link_manager_status_option_2"),
        tcg.lib.prop("ac_link_manager_status_option_3"),
      ];

    //	Result
    for (var i = 0; i < entry.configs.length; i++) {
      list += '<dl class="dira-lm-affdtl-gameItem">';
      list +=
        '<dt class="dira-lm-affdtl-gameName">' + tcg.lib.prop("series_label_" + entry.configs[i].gameCode) + "</dt>";
      list += '<dd class="dira-lm-affdtl-gameRebate">' + entry.configs[i].rebate + "</dd>";
      list += "</dl>";
    }

    _html =
      "<div class='dira-lm-affdtl x-pop'> \
				<h2>" +
      tcg.lib.prop("ac_bonus_group_details") +
      "</h2> \
				<dl class='dira-lm-affdtl-main'> \
					<dt>" +
      tcg.lib.prop("ac_register_link") +
      "</dt><dd class='regLink'>" +
      tcg.ac.ui.generateAffiliateUrl(entry.code).url +
      "</dd> \
					<dt>" +
      tcg.lib.prop("ac_link_manager_type_label") +
      "</dt><dd class='regType'>" +
      TYPE[entry.type] +
      "</dd> \
					<dt>" +
      tcg.lib.prop("ac_link_manager_status_label") +
      "</dt><dd class='regStatus'>" +
      STATUS[entry.status] +
      "</dd> \
				</dl> \
				<div class='dira-lm-affdtl-games'>" +
      list +
      "</div> \
			</div>";

    return _html;
  };

  tcg.ac.ui.graphify = function (date, moneyRange, moneyList, draw_line_color, callback) {
    var c = document.getElementById("chart-canvas");
    var ctx = c.getContext("2d");
    var percentage = [];
    var height = c.height;
    var textWidth = 0;
    var offsetX = c.width * 0.13;
    var offsetY = c.height * 0.2;
    var textYPos = c.height * 0.9;
    var totalAreaOfGraph = height * 0.7;
    var totalAreaOfGraphX = c.width * 0.9;
    draw_line_color = draw_line_color || "#4beac0";
    ctx.clearRect(0, 0, c.width, c.height);

    function getYpos(perc) {
      return Math.abs(totalAreaOfGraph * perc - Math.abs(height - offsetY));
    }

    var drawText = function (t, size, x, y, color) {
      ctx.textAlign = "right";
      ctx.font = size + "px Arial";
      ctx.fillStyle = color;
      textWidth = ctx.measureText(t).width;
      ctx.fillText(t, x, y);
    };
    var drawLine = function (startX, startY, endX, endY, lineWidth, color) {
      ctx.beginPath();
      ctx.moveTo(startX, startY);
      ctx.lineTo(endX, endY);
      ctx.strokeStyle = color;
      ctx.lineWidth = lineWidth;
      ctx.stroke();
    };
    var drawDot = function (x, y) {
      ctx.beginPath();
      ctx.arc(x, y, 4.5, 2 * Math.PI, false);
      ctx.fillStyle = draw_line_color;
      ctx.fill();
    };
    var drawGraph = function (data) {
      for (var l = 0; l < data.length; l++) {
        ctx.beginPath();
        ctx.strokeStyle = draw_line_color;
        ctx.moveTo(data[l].x - 40, data[l].y);

        if (l < data.length - 1) {
          ctx.lineTo(data[l + 1].x - 40, data[l + 1].y);
        }
        ctx.lineWidth = 3;
        ctx.stroke();

        var newDot = new drawDot(data[l].x - 40, data[l].y);
      }
    };
    var largest = Math.max.apply(Math, moneyList);
    for (var h = 0; h < moneyList.length; h++) {
      var v = 0;
      if (isNaN(Number(moneyList[h] / largest).toFixed(2))) {
        v = 0;
      } else {
        v = Number(moneyList[h] / largest).toFixed(2);
      }
      percentage.push(v);
    }
    var lineOffsetY = Math.abs(height - offsetY);
    var lineDistanceY = totalAreaOfGraph * 0.25;
    var textXPos = c.width * 0.05;
    var textOffsetY = totalAreaOfGraph * 0.18;
    var textOffsetX = c.height * 0.17;
    var textPositions = [];
    var lineStart = c.width * 0.055;
    var lineEnd = c.width * 0.97;
    for (var i = 0; i < moneyRange.length; i++) {
      var newLine;
      var yPos = lineOffsetY - i * lineDistanceY;
      if (i == 0) {
        newLine = new drawLine(lineStart, yPos, lineEnd, yPos, 2, "#8d9091");
      } else {
        newLine = new drawLine(lineStart, yPos, lineEnd, yPos, 2, "#e4eaed");
      }

      textPositions.push({
        x: textXPos,
        y: textOffsetY + i * lineDistanceY - 5,
        t: largest < 50 ? moneyRange[moneyRange.length - (i + 1)] : Math.floor(moneyRange[moneyRange.length - (i + 1)]),
      });
    }
    for (var j = 0; j < textPositions.length; j++) {
      var valueText = new drawText(
        textPositions[j].t,
        Math.floor(c.height * 0.044).toString(),
        textPositions[j].x,
        textPositions[j].y,
        "#848484"
      );
    }
    for (var k = 0; k < date.length; k++) {
      var dateText = new drawText(
        date[k],
        Math.floor(c.height * 0.044).toString(),
        c.width * 0.13 + k * ((c.width - textWidth) / date.length),
        textYPos,
        "#848484"
      );
    }
    var targetXPos = 0;
    var targetYPos = 0;
    var graphData = [];
    for (var l = 0; l < date.length; l++) {
      targetXPos = c.width * 0.13 + l * ((c.width - textWidth) / date.length);
      targetYPos = getYpos(percentage[l]);
      graphData.push({ x: targetXPos, y: targetYPos });
    }
    var newGraph = new drawGraph(graphData);

    if (callback) {
      callback();
    }
  };

  tcg.ac.ui._btn_loader_show = function (elem) {
    $(elem).attr("data-old-content", $(elem).html());
    $(elem).html('<div class="_loader"></div>');
    $(elem).prop("disabled", true);
  };

  tcg.ac.ui._btn_loader_hide = function () {
    var elem = $("._loader").parent();
    $(elem).html($(elem).attr("data-old-content"));
    $(elem).prop("disabled", false);
  };

  tcg.ac.ui.strPad = function (i, l, s) {
    var o = i.toString();
    if (!s) {
      s = "0";
    }
    while (o.length < l) {
      o = s + o;
    }
    return o;
  };

  tcg.ac.ui.alertSizes = {
    XXS: { width: "298px", height: "230px" },
    XS: { width: "379px", height: "259px" },
    SL: { width: "298px", height: "327px" },
    S: { width: "397px", height: "370px" },
    M: { width: "500px", height: "460px" },
    AM: { width: "500px", height: "490px" },
    L: { width: "561px", height: "580px" },
    CL: { width: "511px", height: "514px" },
    CLL: { width: "347px", height: "365px" },
    depoL: { width: "710px", height: "500px" },
  };
})(window, document, jQuery, tcg);
