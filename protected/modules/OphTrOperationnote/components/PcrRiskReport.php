<?php

/**
 * Created by PhpStorm.
 * User: peter
 * Date: 19/02/16
 * Time: 16:09.
 */
class PcrRiskReport extends Report implements ReportInterface
{
    /**
     * @var string
     */
    protected $searchTemplate = 'application.modules.OphTrOperationnote.views.report.pcr_risk_search';

    /**
     * @var int
     */
    protected $mode;

    /**
     * @param $app
     */
    public function __construct($app)
    {
        $this->mode = $app->getRequest()->getQuery('mode', 0);

        parent::__construct($app);
    }

    protected $plotlyConfig = array(
      'type' => 'scatter',
      'showlegend' => true,
      'paper_bgcolor' => 'rgba(0, 0, 0, 0)',
      'plot_bgcolor' => 'rgba(0, 0, 0, 0)',
      'title' => '',
      'font' => array(
        'family' => 'Roboto,Helvetica,Arial,sans-serif',
      ),
      'xaxis' => array(
        'title' => 'No. Operations',
        'titlefont' => array(
          'size' => 11,
        ),
        'showgrid' => false,
        'ticks' => 'outside',
        'dtick' => 100,
        'tick0' => 0,
      ),
      'yaxis' => array(
        'title' => 'PCR Rate',
        'ticks' => 'outside',
        'dtick' => 10,
        'tick0' => 0,
        'showgrid'=>true,
        'range' => [0,50],
      ),
      'legend'=> array(
        'x' => 0.8,
        'y' => 1,
        'bordercolor' => '#fff',
        'borderwidth' => 1,
        'font' => array(
          'size' => 13
        )
      ),
      'shapes' => array(
        array(
          'type' => 'line',
        'xref' => 'x',
        'yref' => 'y',
        'line' => array(
          'dash' =>'dot',
          'width' => 1,
          'color' => 'rgb(0,0,0)',
          ),
        'x0' => 0,
        'x1' => 1000,
        'y0' => 0,
        'y1' => 0,
      )
      ),
    );

    protected $totalOperations = 1000;
    /**
     * @param $surgeon
     * @param $dateFrom
     * @param $dateTo
     *
     * @return CDbDataReader|mixed
     */
    protected function queryData($surgeon, $dateFrom, $dateTo)
    {
        $this->command->reset();
        $this->command->select('ophtroperationnote_cataract_complications.name as complication, pcr_risk as risk')
            ->from('et_ophtroperationnote_cataract')
            ->join('event', 'et_ophtroperationnote_cataract.event_id = event.id')
            ->join('et_ophtroperationnote_surgeon', 'et_ophtroperationnote_surgeon.event_id = event.id')
            ->leftJoin('ophtroperationnote_cataract_complication', 'et_ophtroperationnote_cataract.id = ophtroperationnote_cataract_complication.cataract_id')
            ->leftJoin('ophtroperationnote_cataract_complications', 'ophtroperationnote_cataract_complications.id = ophtroperationnote_cataract_complication.complication_id')
            ->where('surgeon_id = :surgeon', array('surgeon' => $surgeon))
            ->andWhere('event.deleted=0');

        if ($dateFrom) {
            $this->command->andWhere('event.event_date >= :dateFrom', array('dateFrom' => $dateFrom));
        }

        if ($dateTo) {
            $this->command->andWhere('event.event_date <= :dateTo', array('dateTo' => $dateTo));
        }

        return $this->command->queryAll();
    }
    protected function querySurgeonData(){
        $this->command->reset();
        $this->command->select('user.id as id')
            ->from('user')
            ->where('is_surgeon = 1');
        return $this->command->queryAll();
    }

    /**
     * @return array
     */
    public function dataSet()
    {
        $return_data = array();
        if ($this->allSurgeons){
            $surgeon_id_list =  $this->querySurgeonData();
        }else{
            $surgeon_id_list = array(array('id' => $this->surgeon));
        }

        foreach ($surgeon_id_list as $surgeon_id) {
            $data = $this->queryData($surgeon_id['id'], $this->from, $this->to);
            $total = $this->getTotalOperations($surgeon_id['id']);
            $pcrCases = 0;
            $pcrRiskTotal = 0;
            $adjustedPcrRate = 0;

            foreach ($data as $case) {
                if (isset($case['complication']) && ($case['complication'] === 'PC rupture' || $case['complication'] === 'PC rupture with vitreous loss' || $case['complication'] === 'PC rupture no vitreous loss')) {
                    ++$pcrCases;
                }
                if (isset($case['risk']) && $case['risk'] !== '' && $case['risk'] != 0) {
                    $pcrRiskTotal += $case['risk'];
                } else {
                    $pcrRiskTotal += 1.92;
                }
            }

            if ($total !== 0 && (int)$pcrRiskTotal !== 0) {
                // unadjusted PCR rate
                $unadjustedPcrRate = ($pcrCases / $total) * 100;

                // adjusted PCR rate
                $expectedPcrRate = $pcrRiskTotal / $total;
                $observedPcrRate = $pcrCases / $total;
                $observedExpectedRate = $observedPcrRate / $expectedPcrRate;
                $adjustedPcrRate = ($observedExpectedRate * $this->average()) * 100; // we need to return %
                //$adjustedPcrRate = (($pcrCases / $total) / ($pcrRiskTotal / $total)) * $this->average();
            }

            // set the graph subtitle here, so we don't have to run this query more than once
            if ($total > 1000) {
                $this->totalOperations = $total;
            }


            if ($this->mode == 0) {
                array_push($return_data, array('name' => 'adjusted', 'x' => $total, 'y' => $adjustedPcrRate));
            } elseif ($this->mode == 1) {
                array_push($return_data, array('name' => 'unadjusted', 'x' => $total, 'y' => $unadjustedPcrRate));
            } elseif ($this->mode == 2) {
                array_push($return_data, array('name' => 'unadjusted', 'x' => $total, 'y' => $unadjustedPcrRate), array('name' => 'adjusted', 'x' => $total, 'y' => $adjustedPcrRate));
            }
        }
        return $return_data;
    }
    /**
     * @return string
     */

    public function tracesJson(){
      $dataset =$this->dataSet();
      $trace1 = array(
        'name' => 'Current Surgeon',
        'mode'=>'markers',
        'type' => 'scatter',
        'x' => array_map(function($item){
          return $item['x'];
        }, $dataset),
        'y' => array_map(function($item){
          return $item['y'];
        }, $dataset),
        'hovertext' => array_map(function($item){
          return '<b>PCR Risk adjusted</b><br><i>Operations:</i>'
            . $item['x'] . '<br><i>PCR Avg:</i>'
            . number_format($item['y'], 2);
        }, $dataset),
        'hoverinfo'=>'text',
        'hoverlabel' => array(
          'bgcolor' => '#fff',
          'bordercolor' => '#1f77b4',
          'font' => array(
            'color' => '#000',
          ),
        ),
      );
      $trace2 = array(
        'name' => 'Upper 99.8%',
        'line' => array(
          'color' => 'red',
        ),
        'x'=> array_map(function ($item){
          return $item[0];
        }, $this->upper98()),
        'y' => array_map(function ($item){
          return $item[1];
        }, $this->upper98()),
        'hoverinfo' => 'skip',
      );
      $trace3 = array(
        'name' => 'Upper 95%',
        'line' => array(
          'color' => 'green',
        ),
        'x'=> array_map(function ($item){
                    return $item[0];
              }, $this->upper95()),
        'y' => array_map(function ($item){
          return $item[1];
        }, $this->upper95()),
        'hoverinfo' => 'skip',
      );

      $traces = array($trace1, $trace2, $trace3);
      return json_encode($traces);

    }

    /**
     * @return int
     */
    public function getTotalOperations($surgeon)
    {
        $this->command->reset();
        $this->command->select('COUNT(*) as total')
            ->from('et_ophtroperationnote_cataract')
            ->join('event', 'et_ophtroperationnote_cataract.event_id = event.id')
            ->join('et_ophtroperationnote_surgeon', 'et_ophtroperationnote_surgeon.event_id = event.id')
            ->where('event.deleted=0');

        if ($surgeon !== 'all'){
            $this->command->andwhere('surgeon_id = :surgeon', array('surgeon' => $surgeon));
        }

        if ($this->from) {
            $this->command->andWhere('event.event_date >= :dateFrom', array('dateFrom' => $this->from));
        }

        if ($this->to) {
            $this->command->andWhere('event.event_date <= :dateTo', array('dateTo' => $this->to));
        }

        $totalData = $this->command->queryAll();

        return (int) $totalData[0]['total'];
    }
    /**
     * @return string
     */

    public function plotlyConfig(){
      if ($this->mode == 0) {
        $this->plotlyConfig['shapes'][0]['y0'] = $this->average();
        $this->plotlyConfig['shapes'][0]['y1'] = $this->average();
      }
      if ($this->allSurgeons){
          $totalOperations = $this->getTotalOperations('all');
      }else{
          $totalOperations = $this->getTotalOperations($this->surgeon);
      }
      $this->plotlyConfig['title'] = 'PCR Rate (risk adjusted)<br><sub>Total Operations: '
        .$totalOperations.'</sub>';
      return json_encode($this->plotlyConfig);
    }

    /**
     * @return array
     */
    protected function upper98()
    {
        return array(
            array(10, 95.8871806616717),
            array(15, 86.4396662303291),
            array(20, 74.6356114609427),
            array(25, 63.4537505849973),
            array(30, 54.0491067913775),
            array(35, 46.4974490643753),
            array(40, 40.5092059647093),
            array(45, 35.7468561114294),
            array(50, 31.9228704509352),
            array(55, 28.8148739964),
            array(60, 26.2565789060309),
            array(65, 24.1246851865777),
            array(70, 22.3274992386786),
            array(75, 20.796267460001),
            array(80, 19.4788865213385),
            array(85, 18.3354140667468),
            array(90, 17.3348725861735),
            array(95, 16.452963856708),
            array(100, 15.6704214940816),
            array(105, 14.9718119179159),
            array(110, 14.3446525420569),
            array(115, 13.7787563489288),
            array(120, 13.2657395971442),
            array(125, 12.7986482766835),
            array(130, 12.3716718825352),
            array(135, 11.9799220383443),
            array(140, 11.6192597505044),
            array(145, 11.2861594703589),
            array(150, 10.9776012653698),
            array(155, 10.6909846392074),
            array(160, 10.4240591608629),
            array(165, 10.1748682457766),
            array(170, 9.94170330320513),
            array(175, 9.7230661111644),
            array(180, 9.51763776484646),
            array(185, 9.32425291008928),
            array(190, 9.14187825151203),
            array(195, 8.96959453783705),
            array(200, 8.80658139107971),
            array(205, 8.65210447369125),
            array(210, 8.50550458723982),
            array(215, 8.36618837439042),
            array(220, 8.23362035771997),
            array(225, 8.10731609798878),
            array(230, 7.98683629369826),
            array(235, 7.87178167524251),
            array(240, 7.76178857235613),
            array(245, 7.65652505414282),
            array(250, 7.55568755772704),
            array(255, 7.45899793527214),
            array(260, 7.36620086035833),
            array(265, 7.27706154398726),
            array(270, 7.19136371815297),
            array(275, 7.10890785129274),
            array(280, 7.02950956524296),
            array(285, 6.95299822776776),
            array(290, 6.87921569845673),
            array(295, 6.80801520892564),
            array(300, 6.73926036090469),
            array(305, 6.67282422804284),
            array(310, 6.60858854916322),
            array(315, 6.54644300232846),
            array(320, 6.48628455046101),
            array(325, 6.42801685045167),
            array(330, 6.37154971870804),
            array(335, 6.31679864697245),
            array(340, 6.26368436299519),
            array(345, 6.2121324313031),
            array(350, 6.16207288987048),
            array(355, 6.11343991899166),
            array(360, 6.06617153908256),
            array(365, 6.02020933451264),
            array(370, 5.97549820089454),
            array(375, 5.93198611354481),
            array(380, 5.88962391507954),
            array(385, 5.84836512032894),
            array(390, 5.80816573694886),
            array(395, 5.76898410027801),
            array(400, 5.73078072114087),
            array(405, 5.69351814542943),
            array(410, 5.65716082441583),
            array(415, 5.6216749948525),
            array(420, 5.58702856801011),
            array(425, 5.55319102688664),
            array(430, 5.52013333089505),
            array(435, 5.48782782740306),
            array(440, 5.45624816955799),
            array(445, 5.42536923988212),
            array(450, 5.39516707917195),
            array(455, 5.3656188202771),
            array(460, 5.33670262637278),
            array(465, 5.30839763337446),
            array(470, 5.28068389617434),
            array(475, 5.25354233840692),
            array(480, 5.22695470547673),
            array(485, 5.20090352060364),
            array(490, 5.1753720436623),
            array(495, 5.15034423261052),
            array(500, 5.12580470731878),
            array(505, 5.10173871562836),
            array(510, 5.07813210147938),
            array(515, 5.05497127496314),
            array(520, 5.0322431841645),
            array(525, 5.00993528867075),
            array(530, 4.988035534633),
            array(535, 4.96653233127486),
            array(540, 4.9454145287516),
            array(545, 4.92467139726977),
            array(550, 4.90429260738448),
            array(555, 4.88426821139753),
            array(560, 4.86458862578523),
            array(565, 4.84524461459005),
            array(570, 4.82622727371488),
            array(575, 4.80752801606322),
            array(580, 4.78913855747251),
            array(585, 4.77105090339175),
            array(590, 4.75325733625763),
            array(595, 4.735750403527),
            array(600, 4.71852290632601),
            array(605, 4.70156788867925),
            array(610, 4.68487862728447),
            array(615, 4.668448621801),
            array(620, 4.65227158562183),
            array(625, 4.63634143710164),
            array(630, 4.6206522912145),
            array(635, 4.60519845161697),
            array(640, 4.58997440309374),
            array(645, 4.57497480436446),
            array(650, 4.5601944812317),
            array(655, 4.54562842005139),
            array(660, 4.53127176150797),
            array(665, 4.51711979467806),
            array(670, 4.50316795136673),
            array(675, 4.4894118007023),
            array(680, 4.4758470439757),
            array(685, 4.46246950971153),
            array(690, 4.44927514895908),
            array(695, 4.43626003079147),
            array(700, 4.42342033800265),
            array(705, 4.41075236299188),
            array(710, 4.39825250382639),
            array(715, 4.38591726047314),
            array(720, 4.37374323119141),
            array(725, 4.36172710907803),
            array(730, 4.34986567875801),
            array(735, 4.33815581321324),
            array(740, 4.32659447074275),
            array(745, 4.31517869204802),
            array(750, 4.3039055974376),
            array(755, 4.29277238414508),
            array(760, 4.28177632375535),
            array(765, 4.27091475973383),
            array(770, 4.26018510505415),
            array(775, 4.24958483991943),
            array(780, 4.23911150957308),
            array(785, 4.228762722195),
            array(790, 4.21853614687924),
            array(795, 4.20842951168947),
            array(800, 4.19844060178887),
            array(805, 4.18856725764103),
            array(810, 4.17880737327879),
            array(815, 4.16915889463795),
            array(820, 4.15961981795315),
            array(825, 4.15018818821315),
            array(830, 4.14086209767295),
            array(835, 4.13163968442031),
            array(840, 4.12251913099443),
            array(845, 4.11349866305448),
            array(850, 4.104576548096),
            array(855, 4.09575109421305),
            array(860, 4.08702064890432),
            array(865, 4.0783835979213),
            array(870, 4.06983836415684),
            array(875, 4.06138340657247),
            array(880, 4.05301721916281),
            array(885, 4.04473832995569),
            array(890, 4.03654530004649),
            array(895, 4.02843672266536),
            array(900, 4.02041122227592),
            array(905, 4.01246745370449),
            array(910, 4.00460410129817),
            array(915, 3.99681987811117),
            array(920, 3.98911352511796),
            array(925, 3.98148381045223),
            array(930, 3.97392952867091),
            array(935, 3.96644950004195),
            array(940, 3.95904256985528),
            array(945, 3.95170760775588),
            array(950, 3.94444350709813),
            array(955, 3.93724918432086),
            array(960, 3.93012357834201),
            array(965, 3.92306564997246),
            array(970, 3.91607438134816),
            array(975, 3.90914877537998),
            array(980, 3.90228785522057),
            array(985, 3.89549066374769),
            array(990, 3.88875626306332),
            array(995, 3.88208373400813),
            array(1000, 3.87547217569059),
            array(ceil($this->totalOperations / 100) * 100, 3.87547217569059),
        );
    }

    /**
     * @return array
     */
    protected function upper95()
    {
        return array(
            array(10, 63.7561697152892),
            array(15, 43.5917580036547),
            array(20, 32.1239597131496),
            array(25, 25.2995206386449),
            array(30, 20.9207773907284),
            array(35, 17.9214181657854),
            array(40, 15.7569346172495),
            array(45, 14.1291624704059),
            array(50, 12.8639950267371),
            array(55, 11.8540203238157),
            array(60, 11.0298304025536),
            array(65, 10.3447871194499),
            array(70, 9.76649439540737),
            array(75, 9.2717999225461),
            array(80, 8.8437460496278),
            array(85, 8.46964369653948),
            array(90, 8.13981787210272),
            array(95, 7.84676826211428),
            array(100, 7.5845939386266),
            array(105, 7.34859056696254),
            array(110, 7.13496292805125),
            array(115, 6.94061616447202),
            array(120, 6.76300179974401),
            array(125, 6.60000252982235),
            array(130, 6.44984489524052),
            array(135, 6.31103229264003),
            array(140, 6.18229302176178),
            array(145, 6.06253958338835),
            array(150, 5.95083649170397),
            array(155, 5.84637459778878),
            array(160, 5.7484504408696),
            array(165, 5.65644951716853),
            array(170, 5.56983262721259),
            array(175, 5.48812466140731),
            array(180, 5.41090533118207),
            array(185, 5.33780146342384),
            array(190, 5.26848055929374),
            array(195, 5.20264538201466),
            array(200, 5.14002938695326),
            array(205, 5.08039284500547),
            array(210, 5.02351953964282),
            array(215, 4.9692139409857),
            array(220, 4.91729877842201),
            array(225, 4.86761294769794),
            array(230, 4.82000969990972),
            array(235, 4.77435506905672),
            array(240, 4.7305265022652),
            array(245, 4.68841166283133),
            array(250, 4.64790738115299),
            array(255, 4.60891873264644),
            array(260, 4.57135822505415),
            array(265, 4.53514508028206),
            array(270, 4.50020459816848),
            array(275, 4.46646759147035),
            array(280, 4.43386988292478),
            array(285, 4.40235185656158),
            array(290, 4.37185805655016),
            array(295, 4.34233682779835),
            array(300, 4.31373999331167),
            array(305, 4.28602256399222),
            array(310, 4.25914247712802),
            array(315, 4.23306036031083),
            array(320, 4.20773931793811),
            array(325, 4.18314473781311),
            array(330, 4.15924411566503),
            array(335, 4.13600689567732),
            array(340, 4.11340432534207),
            array(345, 4.09140932315761),
            array(350, 4.06999635785948),
            array(355, 4.0491413380258),
            array(360, 4.02882151102917),
            array(365, 4.00901537042249),
            array(370, 3.98970257094637),
            array(375, 3.97086385043434),
            array(380, 3.95248095796956),
            array(385, 3.9345365877153),
            array(390, 3.91701431790157),
            array(395, 3.89989855450385),
            array(400, 3.88317447919688),
            array(405, 3.86682800120861),
            array(410, 3.8508457127363),
            array(415, 3.83521484762023),
            array(420, 3.81992324299958),
            array(425, 3.80495930370167),
            array(430, 3.79031196913907),
            array(435, 3.77597068251032),
            array(440, 3.76192536211854),
            array(445, 3.74816637463956),
            array(450, 3.73468451018594),
            array(455, 3.72147095902733),
            array(460, 3.70851728983967),
            array(465, 3.69581542936691),
            array(470, 3.68335764338904),
            array(475, 3.67113651889907),
            array(480, 3.65914494740016),
            array(485, 3.6473761092411),
            array(490, 3.6358234589154),
            array(495, 3.62448071125524),
            array(500, 3.61334182845717),
            array(505, 3.6024010078813),
            array(510, 3.59165267057061),
            array(515, 3.58109145044105),
            array(520, 3.57071218409691),
            array(525, 3.56050990122951),
            array(530, 3.55047981556045),
            array(535, 3.54061731629356),
            array(540, 3.53091796004232),
            array(545, 3.52137746320215),
            array(550, 3.51199169473902),
            array(555, 3.502756669368),
            array(560, 3.49366854109734),
            array(565, 3.4847235971151),
            array(570, 3.4759182519976),
            array(575, 3.46724904221954),
            array(580, 3.45871262094797),
            array(585, 3.4503057531027),
            array(590, 3.44202531066753),
            array(595, 3.43386826823743),
            array(600, 3.42583169878775),
            array(605, 3.41791276965281),
            array(610, 3.41010873870165),
            array(615, 3.4024169506997),
            array(620, 3.394834833846),
            array(625, 3.38735989647597),
            array(630, 3.37998972392056),
            array(635, 3.37272197551323),
            array(640, 3.36555438173653),
            array(645, 3.35848474150084),
            array(650, 3.35151091954806),
            array(655, 3.34463084397357),
            array(660, 3.33784250386028),
            array(665, 3.3311439470187),
            array(670, 3.32453327782775),
            array(675, 3.31800865517079),
            array(680, 3.31156829046222),
            array(685, 3.30521044575989),
            array(690, 3.29893343195901),
            array(695, 3.2927356070635),
            array(700, 3.28661537453086),
            array(705, 3.28057118168689),
            array(710, 3.27460151820695),
            array(715, 3.26870491466039),
            array(720, 3.26287994111512),
            array(725, 3.25712520579943),
            array(730, 3.25143935381838),
            array(735, 3.245821065922),
            array(740, 3.24026905732313),
            array(745, 3.23478207656226),
            array(750, 3.22935890441743),
            array(755, 3.22399835285703),
            array(760, 3.21869926403348),
            array(765, 3.21346050931598),
            array(770, 3.2082809883606),
            array(775, 3.2031596282159),
            array(780, 3.19809538246266),
            array(785, 3.19308723038609),
            array(790, 3.18813417617912),
            array(795, 3.18323524817541),
            array(800, 3.17838949811079),
            array(805, 3.17359600041195),
            array(810, 3.16885385151106),
            array(815, 3.16416216918539),
            array(820, 3.15952009192079),
            array(825, 3.15492677829797),
            array(830, 3.15038140640075),
            array(835, 3.14588317324523),
            array(840, 3.14143129422915),
            array(845, 3.13702500260047),
            array(850, 3.1326635489445),
            array(855, 3.12834620068878),
            array(860, 3.12407224162495),
            array(865, 3.11984097144705),
            array(870, 3.11565170530541),
            array(875, 3.11150377337572),
            array(880, 3.10739652044252),
            array(885, 3.10332930549658),
            array(890, 3.09930150134574),
            array(895, 3.09531249423851),
            array(900, 3.09136168350007),
            array(905, 3.08744848118011),
            array(910, 3.0835723117122),
            array(915, 3.07973261158402),
            array(920, 3.07592882901825),
            array(925, 3.07216042366367),
            array(930, 3.06842686629597),
            array(935, 3.06472763852813),
            array(940, 3.06106223252974),
            array(945, 3.05743015075523),
            array(950, 3.05383090568042),
            array(955, 3.05026401954732),
            array(960, 3.04672902411664),
            array(965, 3.04322546042798),
            array(970, 3.03975287856722),
            array(975, 3.03631083744101),
            array(980, 3.03289890455798),
            array(985, 3.02951665581657),
            array(990, 3.02616367529908),
            array(995, 3.02283955507188),
            array(1000, 3.01954389499152),
            array(ceil($this->totalOperations / 100) * 100, 3.01954389499152),
        );
    }

    /**
     * @return float
     */
    protected function average()
    {
        return 1.92;
    }

    /**
     * @return mixed|string
     */
    public function renderSearch($analytics = false)
    {
        if ($analytics){
            $this->searchTemplate = 'application.modules.OphTrOperationnote.views.report.pcr_risk_search_analytics';
        }

        $displayModes = array(array('id' => '0', 'name' => 'Adjusted risk'), array('id' => '1', 'name' => 'Unadjusted risk'), array('id' => '2', 'name' => 'Both'));

        return $this->app->controller->renderPartial($this->searchTemplate, array('report' => $this, 'modes' => $displayModes));
    }
}
