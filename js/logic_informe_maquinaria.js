$(document).ready(function() {
    let rankingTable, barChart, monthlyTable, lineChart;

    $('#filtro-maquinaria-form').on('submit', function(e) {
        e.preventDefault();
        const fechaInicio=$('#fecha_inicio').val(), fechaFin=$('#fecha_fin').val(), maquinaId=$('#maquina').val();
        const resultadoDiv=$('#resultado-informe'), botonesGlobalesDiv=$('#contenedor-global-botones');
        if (!fechaInicio || !fechaFin) { return; }

        resultadoDiv.html('<p class="alert alert-info">Generando informe...</p>');
        botonesGlobalesDiv.empty();
        if (rankingTable) rankingTable.destroy();
        if (monthlyTable) monthlyTable.destroy();
        if (barChart) barChart.destroy();
        if (lineChart) lineChart.destroy();

        $.ajax({
            url: 'api_informe_maquinaria.php',
            type: 'GET',
            data: { fecha_inicio: fechaInicio, fecha_fin: fechaFin, id_maquina: maquinaId },
            dataType: 'json',
            success: function(response) {
                resultadoDiv.empty();
                if (!response.rankingUnidades || response.rankingUnidades.length === 0) {
                    resultadoDiv.html('<p class="alert alert-info">No se encontraron datos...</p>'); return;
                }
                resultadoDiv.html(`
                    <div class="seccion-consulta" id="ranking-container"></div>
                    <div class="seccion-consulta" id="barchart-container"></div>
                    <div class="seccion-consulta" id="monthly-container"></div>
                    <div class="seccion-consulta" id="linechart-container"></div>
                `);
                rankingTable = renderRanking(response.rankingUnidades);
                barChart = renderBarChart(response.rankingUnidades);
                monthlyTable = renderMonthly(response.monthlyUnidades);
                lineChart = renderLineChart(response.lineChartData);
                renderizarBotonesGlobales({rankingTable, monthlyTable, barChart, lineChart}, {fechaInicio, fechaFin});
            },
            error: function() { resultadoDiv.html('<p class="alert alert-danger">Error de conexión.</p>'); }
        });
    });

    const numberFormat = $.fn.dataTable.render.number('.', ',', 0, '');
    function renderRanking(data) {if(!data||!data.length){$('#ranking-container').html('<h3>Ranking de Unidades</h3><p class="alert alert-info">No hay datos.</p>');return null}$('#ranking-container').html(`<h3>Ranking de Unidades Producidas</h3><div class="tabla-contenedor"><table id="tabla-ranking-unidades" class="tabla-registro" style="width:100%"><thead><tr><th>Máquina</th><th>Total Unidades</th></tr></thead></table></div>`);return $('#tabla-ranking-unidades').DataTable({data:data,columns:[{data:'maquina_etiqueta'},{data:'total_unidades',render:numberFormat}],dom:'t',language:{url:'js/i18n/es-ES.json'},paging:false,info:false,searching:false,order:[[1,'desc']]});}
    function renderBarChart(data) {if(!data||!data.length){$('#barchart-container').html('<h3>Gráfico de Unidades</h3><p class="alert alert-info">No hay datos.</p>');return null}$('#barchart-container').html(`<h3>Gráfico de Unidades Totales</h3><div class="tabla-contenedor" style="padding:15px;height:400px"><canvas id="unidadesBarChart"></canvas></div>`);return new Chart($('#unidadesBarChart').get(0).getContext('2d'),{type:'bar',data:{labels:data.map(i=>i.maquina_etiqueta),datasets:[{label:'Total Unidades',data:data.map(i=>i.total_unidades),backgroundColor:'rgba(40, 167, 69, 0.6)'}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false},title:{display:true,text:'Total de Unidades por Máquina'}},scales:{y:{beginAtZero:true,ticks:{precision:0}}}}});}
    function renderMonthly(data) {if(!data||!data.length){$('#monthly-container').html('<h3>Desglose Mensual de Unidades</h3><p class="alert alert-info">No hay datos.</p>');return null}$('#monthly-container').html(`<h3>Desglose de Unidades por Mes</h3><div class="tabla-contenedor"><table id="tabla-mensual-unidades" class="tabla-registro" style="width:100%"><thead><tr><th>Máquina</th><th>Ene</th><th>Feb</th><th>Mar</th><th>Abr</th><th>May</th><th>Jun</th><th>Jul</th><th>Ago</th><th>Sep</th><th>Oct</th><th>Nov</th><th>Dic</th></tr></thead></table></div>`);return $('#tabla-mensual-unidades').DataTable({data:data,columns:[{data:'maquina_etiqueta'},{data:'Ene',render:numberFormat},{data:'Feb',render:numberFormat},{data:'Mar',render:numberFormat},{data:'Abr',render:numberFormat},{data:'May',render:numberFormat},{data:'Jun',render:numberFormat},{data:'Jul',render:numberFormat},{data:'Ago',render:numberFormat},{data:'Sep',render:numberFormat},{data:'Oct',render:numberFormat},{data:'Nov',render:numberFormat},{data:'Dic',render:numberFormat}],dom:'t',language:{url:'js/i18n/es-ES.json'},paging:false,info:false,searching:false});}
    function renderLineChart(chartData) {if(!chartData||!chartData.labels||!chartData.labels.length){$('#linechart-container').html('<h3>Gráfico de Tendencia</h3><p class="alert alert-info">No hay datos.</p>');return null}$('#linechart-container').html(`<h3>Tendencia Mensual de Unidades</h3><div class="tabla-contenedor" style="padding:15px;height:500px;"><canvas id="tendenciasChart"></canvas></div>`);const aColores=['#007bff','#28a745','#dc3545','#ffc107','#17a2b8','#6f42c1'];chartData.datasets.forEach((d,i)=>{d.borderColor=aColores[i%aColores.length];d.fill=false;d.tension=0.1;});return new Chart(document.getElementById('tendenciasChart').getContext('2d'),{type:'line',data:chartData,options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{position:'top'},title:{display:true,text:'Unidades Producidas por Mes'}},scales:{y:{beginAtZero:true}}}});}

    function renderizarBotonesGlobales(elementos, fechas) {
        const botonesDiv=$('#contenedor-global-botones');
        botonesDiv.html(`
            <div class="seccion-formulario" style="display:flex;gap:5px;justify-content:flex-end;flex-wrap:wrap;align-items:center;">
                <span style="font-weight:bold;">RANKING:</span><button id="dlRanking" class="btn btn-success" title="Exportar a Excel"><i class="fas fa-file-excel"></i></button><button id="prRanking" class="btn btn-info" title="Imprimir"><i class="fas fa-print"></i></button>
                <span style="font-weight:bold;margin-left:10px;">DESGLOSE MENSUAL:</span><button id="dlMensual" class="btn btn-success" title="Exportar a Excel"><i class="fas fa-file-excel"></i></button><button id="prMensual" class="btn btn-info" title="Imprimir"><i class="fas fa-print"></i></button>
                <span style="font-weight:bold;margin-left:10px;">GRÁFICO BARRAS:</span><button id="dlGraficoBarras" class="btn btn-success" title="Descargar PNG"><i class="fas fa-download"></i></button><button id="prGraficoBarras" class="btn btn-info" title="Imprimir"><i class="fas fa-print"></i></button>
                <span style="font-weight:bold;margin-left:10px;">GRÁFICO LÍNEAS:</span><button id="dlGraficoLineas" class="btn btn-success" title="Descargar PNG"><i class="fas fa-download"></i></button><button id="prGraficoLineas" class="btn btn-info" title="Imprimir"><i class="fas fa-print"></i></button>
            </div>
        `);
        const nit='900.123.456-7',empresa='BORDADOS DEL SUR S.A.S.';
        const rango=`Del ${fechas.fechaInicio} al ${fechas.fechaFin}`;
        const pie=`Generado por: ${usuarioReporte}`;
        const headExcel=`Empresa: ${empresa}\nNIT: ${nit}\nRango: ${rango}\nFecha: ${fechaReporte}`;
        const headPrint=`<div style="text-align:center;font-family:Arial,sans-serif"><h3>${empresa}</h3><p>NIT: ${nit}<br>${rango}<br>Fecha: ${fechaReporte}</p></div>`;
        
        const setupTableBtns=(t,n,d,p)=>{if(!t)return;new $.fn.dataTable.Buttons(t,{buttons:[{extend:'excelHtml5',className:'buttons-excel',title:n,messageTop:headExcel,messageBottom:pie},{extend:'print',className:'buttons-print',title:n,messageTop:headPrint,messageBottom:pie,customize:(win)=>$(win.document.body).css('background','none').find('h1').css('text-align','center')}]});$(`#${d}`).on('click',()=>t.buttons('.buttons-excel').trigger());$(`#${p}`).on('click',()=>t.buttons('.buttons-print').trigger());};
        setupTableBtns(elementos.rankingTable,'Ranking de Unidades','dlRanking','prRanking');
        setupTableBtns(elementos.monthlyTable,'Desglose Mensual de Unidades','dlMensual','prMensual');

        // ===============================================
        // ===== INICIO DE LA MODIFICACIÓN =====
        // ===============================================

        const descargarGrafico=(grafico)=>{if(grafico){const l=document.createElement('a');l.href=grafico.toBase64Image('image/png',1);l.download=`${grafico.options.plugins.title.text}.png`;l.click();}};
        const imprimirGrafico=(grafico)=>{
            if(grafico){
                const u=grafico.toBase64Image();
                const w=window.open('','_blank');
                const tituloGrafico = grafico.options.plugins.title.text;
                const pieDePaginaImprimir = `<p style="text-align:center;font-size:10px;font-family:Arial,sans-serif;margin-top:20px;">${pie}</p>`;

                w.document.write(`
                    <html>
                        <head><title>Imprimir: ${tituloGrafico}</title></head>
                        <body style="font-family: Arial, sans-serif;">
                            ${headPrint}
                            <h2 style="text-align:center; font-size: 16px; margin-top: 20px;">${tituloGrafico}</h2>
                            <div style="text-align:center; margin-top: 15px;">
                                <img src="${u}" style="max-width: 95%;" onload="window.print(); setTimeout(()=>window.close(),100)">
                            </div>
                            ${pieDePaginaImprimir}
                        </body>
                    </html>`);
                w.document.close();
            }
        };

        $('#dlGraficoBarras').on('click',()=>descargarGrafico(elementos.barChart,'Grafico_Ranking_Unidades'));
        $('#prGraficoBarras').on('click',()=>imprimirGrafico(elementos.barChart));
        $('#dlGraficoLineas').on('click',()=>descargarGrafico(elementos.lineChart,'Grafico_Tendencia_Unidades'));
        $('#prGraficoLineas').on('click',()=>imprimirGrafico(elementos.lineChart));

        // ===============================================
        // ===== FIN DE LA MODIFICACIÓN =====
        // ===============================================
    }
});