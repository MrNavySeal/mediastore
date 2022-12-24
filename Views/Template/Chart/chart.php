<?php
    if($data['chart']=="salespermonth"){
    $sales = $data['sales'];
?>
<script>
    Highcharts.chart('salesMonth', {
        chart: {
            type: 'line'
        },
        title: {
            text: 'Sales from <?=$data['month']." ".$data['year']?>'
        },
        subtitle: {
            text: 'Total: <?=formatNum($data['total'])?>'
        },
        xAxis: {
            categories: [
                <?php
                    
                    for ($i=0; $i < count($sales) ; $i++) { 
                        echo $sales[$i]['day'].",";
                    }
                ?>
            ]
        },
        yAxis: {
            title: {
                text: ''
            }
        },
        plotOptions: {
            line: {
                dataLabels: {
                    enabled: true
                },
                enableMouseTracking: false
            }
        },
        series: [{
            name: '',
            data: [
                <?php
                    
                    for ($i=0; $i < count($sales) ; $i++) { 
                        echo $sales[$i]['total'].",";
                    }
                ?>
            ]
        }]
    });
</script>
<?php }else{
    $salesYear = $data['data'];
?>
<script>
    Highcharts.chart('salesYear', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'Sales from <?=$salesYear[0]['year']?>'
        },
        subtitle: {
            text: 'Total: <?=formatNum($data['total'])?>'
        },
        xAxis: {
            type: 'category',
            labels: {
                rotation: -45,
                style: {
                    fontSize: '13px',
                    fontFamily: 'Verdana, sans-serif'
                }
            }
        },
        yAxis: {
            min: 0,
            title: {
                text: ''
            }
        },
        legend: {
            enabled: false
        },
        tooltip: {
            pointFormat: 'Sales: <b>{point.y:.0f} '+MD+'</b>'
        },
        series: [{
            name: 'Population',
            data: [
                <?php
                    for ($i=0; $i < count($salesYear) ; $i++) { 
                        echo '["'.$salesYear[$i]['month'].'"'.",".''.$salesYear[$i]['sale'].'],';
                    }    
                ?>
                //['Shanghai', 24.2]
            ],
            dataLabels: {
                enabled: true,
                rotation: 0,
                color: '#FFFFFF',
                align: 'center',
                y: 0, // 10 pixels down from the top
                style: {
                    fontSize: '13px',
                    fontFamily: 'system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", "Liberation Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji'
                }
            }
        }]
    });
</script>
<?php }?>