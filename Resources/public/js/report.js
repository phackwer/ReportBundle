var selectReport;
var reportParams;
var tipoImpressao;

$(document).ready(function () {
    $('#j_botoes').hide();
    $('#j_selectReport').change(getOutputTemplate);
    $('#j_pesquisar').click(botaoPesquisar);
    $('#j_limpar').click(limparFormulario);
});

/** Exibe o formulario de pesquisa **/
function getOutputTemplate() {

    if ($('#j_selectReport').val() && $('#j_selectReport').val() != '') {
        $.ajax({
            url: Routing.generate('Ibram_report_get_output_template', {seqReport: $('#j_selectReport').val()})
        })
            .success(function (data) {
                $('#j_formReport').html('');
                $('#j_formReport').append(data);
                $('#j_botoes').show();
            })
            .error(function () {
                $('#j_formReport').html('');
                $('#j_botoes').hide();
            });
    } else {
        $('#j_selectReport').val('');
        $('#j_formReport').html('');

        limpaGrid();

        $('#j_botoes').hide();
    }

}

function botaoPesquisar() {
    selectReport = $('#j_selectReport').val();
    reportParams = $.parseJSON($('#j_' + selectReport).val());
    tipoImpressao = $('#j_tipoImpressao').val();

    if (tipoImpressao == 'grid') {
        exibirGrid();
    }
    if (tipoImpressao == 'pdf') {
        exibirPdf();
    }
    if (tipoImpressao == 'html') {
        exibirHtml();
    }
    if (tipoImpressao == 'xls') {
        exibirXls();
    }
}

function exibirGrid() {
    criaGridDinamica(reportParams);
}

function criaGridDinamica(reportParams) {
    $("#loading").show();
    $.ajax({
        url: Routing.generate('Ibram_report_get_report_grid', {seqReport: selectReport, dataForm: $('#j_form').serialize() })
    })
        .success(function (data) {
            limpaGrid();
            $("#loading").hide();

            $("#j_grid").jqGrid({
                url: Routing.generate('Ibram_report_get_report_grid', {seqReport: selectReport, dataForm: $('#j_form').serialize() }),
                datatype: "json",
                height: '254',
                width: '1000px',
                colNames: data.colNames,
                colModel: data.colModel,
                rowNum: '10',
                scrollOffset: 0,
                forceFit: true,
                hidegrid: false,
                pager: '#pager',
                viewrecords: true,
                loadComplete: function () {
                    if ($(this).getGridParam("records") == 0 || $(this).getGridParam("records") == 1) {
                        $("#dialog-msg").dialog({
                            resizable: false,
                            height: 140,
                            modal: true,
                            buttons: {
                                "Fechar": function () {
                                    $(this).dialog("close");
                                }
                            }}).html('Nenhum registro encontrado com estes parâmetros.');
                        limpaGrid();
                    }
                }
            });
        })
        .error(function () {
            $("#loading").hide();
        });

}

function limpaGrid() {
    $('#j_reportGrid').html('');
    $('#j_reportGrid').html('<div class="box"><table id="j_grid"></table><div id="pager"></div></div>');
}

function exibirPdf() {
    window.open(Routing.generate('Ibram_report_get_report_pdf', {seqReport: selectReport, dataForm: $('#j_form').serialize()  }));
}

function exibirHtml() {
    window.open(Routing.generate('Ibram_report_get_report_html', {seqReport: selectReport, dataForm: $('#j_form').serialize()  }));
}

function exibirXls() {
    window.open(Routing.generate('Ibram_report_get_report_xls', {seqReport: selectReport, dataForm: $('#j_form').serialize()  }));
}

function limparFormulario() {
    document.getElementById('j_form').reset();
}