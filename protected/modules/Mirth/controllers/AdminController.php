<?php

class AdminController extends \ModuleAdminController
{

    private \CDbConnection $conn;

    public const NoError = 0;
    public const CommunicationError = 1;
    public const DataError = 2;

    private array $errorTypes = [
        0 => 'Success',
        1 => 'Communication Error',
        2 => 'Data Error',
    ];

    const Channels = "D_CHANNELS";
    const ChannelDescriptions = "CHANNEL";
    const Messages = "D_M";
    const MessageContents = "D_MC";
    const MessageRoutes = "D_MM";
    const MessageTypes = "D_MCM";

    private array $statuses = [
        'T' => 'Transformed',
        'F' => 'Filtered',
        'S' => 'Sent',
        'E' => 'Error',
    ];

    public $group = 'Mirth';

    public function init()
    {
        $connectionString = Yii::app()->params["mirth_connectionString"];
        $username = Yii::app()->params["mirth_username"];
        $password = Yii::app()->params["mirth_password"];

        $this->conn = new CDbConnection($connectionString, $username, $password);
        parent::init();
    }

    public function accessRules()
    {
        return array(
            array(
                'allow',
                'roles' => array('admin'),
            ),
        );
    }

    public function actionListRoutes()
    {
        $routes = $this->getRoutesForMessage($_GET['message_id'], $_GET['channel_id']);
        $this->pageTitle = 'Routes';
        $this->render('/admin/messages', array('routes' => $routes));
    }

    public function actionList()
    {
        $script = "";
        if (isset($_POST) && isset($_POST["channelId"])) {
            $script = "$('#channel').val('" . $_POST["channelId"] . "');";

            if (isset($_POST["filter"]) && $_POST["filter"] == "true") {
                $script .= "$('#filter').prop( \"checked\", true );;";
            }

            if (isset($_POST["hos_num"])) {
                $script .= "$('#hos_num').val('" . $_POST["hos_num"] . "');";
            }

            $script .= "$('#mirth-log-search').click();";
        }
        $channels = $this->getChannels();
        $this->render('/admin/index', array('channels' => $channels, 'script' => $script));
    }

    public function actionSearch()
    {
        $result = ['data' => null];

        if (isset($_POST)) {
            $data = $this->getData($_POST);
        } else {
            $data = $this->getData();
        }

        if (!empty($data['items'])) {
            $result['data'] = $data['items'];
        } else {
            $result['data'] = null;
        }

        $this->renderJSON($result);
    }

    public function getData($post = array(), $id = false)
    {
        $data = array();

        $check_dateFrom = date_parse_from_format('d M yy', $post["dateFrom"]) ?? "";
        if (checkdate($check_dateFrom['month'], $check_dateFrom['day'], $check_dateFrom['year'])) {
            $dateFrom = date('Y-m-d', strtotime($post["dateFrom"])) . ' 00:00:00';
        } else {
            $dateFrom = "";
        }

        $check_dateTo = date_parse_from_format('d M yy', $post["dateTo"]) ?? "";
        if (checkdate($check_dateTo['month'], $check_dateTo['day'], $check_dateTo['year'])) {
            $dateTo = date('Y-m-d', strtotime($post["dateTo"])) . ' 23:59:59';
        } else {
            $dateTo = "";
        }

        if (!isset($post['channel']) || strcmp($post['channel'], "") === 0) {
            return array();
        } else {
            if (isset($post['filter']) && $post['filter'] == 1
                && ((isset($post['hos_num']) && strcmp($post['hos_num'], "") === 0) || !isset($post['hos_num']))) {
                $messages = $this->getMessages($post['channel'], true, "", $dateFrom, $dateTo);
            } elseif (isset($post['filter']) && $post['filter'] == 0
                && isset($post['hos_num']) && strcmp($post['hos_num'], "") !== 0) {
                $messages = $this->getMessages($post['channel'], false, $post['hos_num'], $dateFrom, $dateTo);
            } elseif (isset($post['filter']) && $post['filter'] == 1
                && isset($post['hos_num']) && strcmp($post['hos_num'], "") !== 0) {
                $messages = $this->getMessages($post['channel'], true, $post['hos_num'], $dateFrom, $dateTo);
            } else {
                $messages = $this->getMessages($post['channel'], false, "", $dateFrom, $dateTo);
            }

            $data['total_items'] = count($messages);
            $data['pages'] = 1;
            $data['page'] = 1;

            if (!isset($post['page'])) {
                $post['page'] = 1;
            }

            $criteria = new CDbCriteria();

            $criteria->order = 't.id desc';
            $criteria->limit = $this->items_per_page;
            if ($id) {
                $criteria->addCondition('t.id > '.(integer) $id);
            } else {
                $criteria->offset = (($post['page'] - 1) * $this->items_per_page);
            }
            $data['pages'] = ceil($data['total_items'] / $this->items_per_page);
            if ($data['pages'] < 1) {
                $data['pages'] = 1;
            }
            if ($post['page'] > $data['pages']) {
                $post['page'] = $data['pages'];
            }
            if (!$id) {
                $data['page'] = $post['page'];
            }

            $data['items'] = $data['files_data'] = $messages;

            return $data;
        }
    }

    public function criteria($count = false)
    {
        $criteria = new CDbCriteria();

        if (@$_REQUEST['channel']) {
            $criteria->addCondition('`dil`.`patient_number` = :hos_num');
            $criteria->params[':hos_num'] = $_REQUEST['hos_num'];
        }

        if (@$_REQUEST['hos_num']) {
            $criteria->addCondition('`dil`.`patient_number` = :hos_num');
            $criteria->params[':hos_num'] = $_REQUEST['hos_num'];
        }

        return $criteria;
    }

    public function actionGetContentTypesForMessage()
    {
        if (isset($_POST) && isset($_POST['messageId'])
            && isset($_POST['channelId']) && isset($_POST['routeId'])) {
            $return['contentTypes'] = $this->getContentTypesForMessage($_POST['messageId'], $_POST['channelId'], $_POST['routeId']);
            $return['messageId'] = $_POST['messageId'];
            $return['channelId'] = $_POST['channelId'];
            $return['routeId'] = $_POST['routeId'];
            $this->renderJSON($return);
            Yii::app()->end();
        } else {
            $this->renderJSON(array());
            Yii::app()->end();
        }
    }

    public function actionGetMessageContent()
    {
        if (isset($_POST) && isset($_POST['messageId']) && isset($_POST['messageContentType'])
            && isset($_POST['channelId']) && isset($_POST['routeId'])) {
            $return['messageContent'] = $this->getMessageContent($_POST['messageId'], $_POST['messageContentType'], $_POST['channelId'], $_POST['routeId']);
            $this->renderJSON($return);
            Yii::app()->end();
        } else {
            $this->renderJSON($_POST);
            Yii::app()->end();
        }
    }

    private function getTableName($prefix, $channelId)
    {
        $channelNumber = $this->conn->createCommand("SELECT LOCAL_CHANNEL_ID FROM ".$this::Channels." WHERE CHANNEL_ID='".$channelId."'")->queryScalar();
        return $prefix.$channelNumber;
    }

    public function getChannels()
    {
        return $this->conn->createCommand("SELECT * FROM ".$this::ChannelDescriptions)->queryAll();
    }

    public function getChannelNames()
    {
        return $this->conn->createCommand("SELECT NAME FROM ".$this::ChannelDescriptions)->queryAll();
    }

    public function getMessages($channelId, $onlyErrors = false, $searchString = '', $dateFrom = '', $dateTo = '')
    {
        $tableName = $this->getTableName($this::Messages, $channelId);
        $routesTableName = $this->getTableName($this::MessageRoutes, $channelId);
        $contentTableName = $this->getTableName($this::MessageContents, $channelId);
        $where = "WHERE 1=1";

        if ($dateFrom != '') {
            $where .= " AND RECEIVED_DATE >= :dateFrom";
        }

        if ($dateTo != '') {
            $where .= " AND RECEIVED_DATE <= :dateTo";
        }

        $messages = $this->conn->createCommand("SELECT * FROM $tableName $where ORDER BY ID DESC")->queryAll(true, array('dateFrom' => $dateFrom, 'dateTo' => $dateTo));

        $messagesFiltered = [];
        foreach ($messages as $id=>$message) {
            $messages[$id]["ERROR"] = $this::NoError;

            $messages[$id]["TYPE"] = $this->getMessageType($message['ID'], $channelId);

            $errNo = $this->conn->createCommand("select count(*) FROM ".$routesTableName." WHERE MESSAGE_ID='".$message["ID"]."' AND STATUS='E'")->queryScalar();
            if ($errNo > 0) {
                $messages[$id]["ERROR"] = $this::CommunicationError;
            }

            $errData = $this->conn->createCommand("select CONTENT from ".$contentTableName." WHERE MESSAGE_ID='".$message['ID']."' AND CONTENT LIKE '%ERR|%'")->queryAll();
            if (count($errData) > 0) {
                $messages[$id]["ERROR"] = $this::DataError;
                $messages[$id]["ERROR_TEXT"] = $errData[0]["CONTENT"];
                $messageLines = explode("#xd;", $errData[0]["CONTENT"]);
                foreach ($messageLines as $messageLine) {
                    if (strpos($messageLine, 'ERR|') !== false) {
                        $messages[$id]["ERROR_TEXT"] = preg_replace('#<[^>]+>#', ' ', $messageLine);
                    }
                }
            }

            $errData = $this->conn->createCommand("select CONTENT from ".$contentTableName." WHERE MESSAGE_ID='".$message['ID']."' AND CONTENT like '%<errors><error>%' and CONTENT not like '%<error><%'")->queryAll();
            if (count($errData) > 0) {
                $messages[$id]["ERROR"] = $this::DataError;
                $messages[$id]["ERROR_TEXT"] = $errData[0]["CONTENT"];
                $messageLines = explode("\n", $errData[0]["CONTENT"]);
                foreach ($messageLines as $messageLine) {
                    if (strpos($messageLine, '<Errors><Error>') !== false) {
                        $messages[$id]["ERROR_TEXT"] = preg_replace('#<[^>]+>#', ' ', $messageLine);
                    }
                }
            }

            $found = false;
            if ($searchString !== '') {
                $search = '%'.$searchString.'%';
                $foundNo = $this->conn->createCommand("select count(*) from ".$contentTableName." WHERE MESSAGE_ID='".$message['ID']."' AND CONTENT LIKE ?")->queryScalar(array($search));
                if ($foundNo > 0) {
                    $found = true;
                }
            }

            if ((!$onlyErrors || $messages[$id]["ERROR"] !== $this::NoError) && ($searchString === '' || ($searchString !== '' && $found))) {
                $messagesFiltered[] = $messages[$id];
                $messagesFiltered[count($messagesFiltered)-1]["ERROR_STRING"] = $this->errorTypes[$messages[$id]["ERROR"]];
            }
        }

        return $messagesFiltered;
    }

    public function getRoutesForMessage($messageId, $channelId)
    {
        $tableName = $this->getTableName($this::MessageRoutes, $channelId);
        $routes = $this->conn->createCommand("SELECT * FROM ".$tableName." WHERE MESSAGE_ID='".$messageId."'")->queryAll();
        foreach ($routes as $id=>$line) {
            $routes[$id]['STATUS'] = $this->statuses[$line['STATUS']];
        }
        return $routes;
    }

    public function getMessageType($messageId, $channelId)
    {
        $tableName = $this->getTableName($this::MessageTypes, $channelId);
        $type = $this->conn->createCommand("SELECT TYPE FROM ".$tableName." WHERE MESSAGE_ID='".$messageId."' LIMIT 1")->queryScalar();
        return $type;
    }

    public function getContentTypesForMessage($messageId, $channelId, $routeId)
    {
        $tableName = $this->getTableName($this::MessageContents, $channelId);
        $contentTypes = $this->conn->createCommand("SELECT CONTENT_TYPE AS ID FROM ".$tableName." WHERE MESSAGE_ID='".$messageId."' AND METADATA_ID='".$routeId."'")->queryAll();
        foreach ($contentTypes as $id=>$contentType) {
            $contentTypes[$id]["NAME"] = $this->getContentType($contentType['ID']);
        }
        return $contentTypes;
    }

    public function getContentType($id)
    {
        $contentTypes = array(
            1=>"RAW",
            2=>"PROCESSED_RAW",
            3=>"TRANSFORMED",
            4=>"ENCODED",
            5=>"SENT",
            6=>"RESPONSE",
            7=>"RESPONSE_TRANSFORMED",
            8=>"PROCESSED_RESPONSE",
            9=>"CONNECTOR_MAP",
            10=>"CHANNEL_MAP",
            11=>"RESPONSE_MAP",
            12=>"PROCESSING_ERROR",
            13=>"POSTPROCESSOR_ERROR",
            14=>"RESPONSE_ERROR",
            15=>"SOURCE_MAP",
        );

        return $contentTypes[$id];
    }

    public function getMessageContent($messageId, $contentTypeId, $channelId, $routeId)
    {
        $tableName = $this->getTableName($this::MessageContents, $channelId);
        $content = $this->conn->createCommand("SELECT CONTENT FROM ".$tableName." WHERE MESSAGE_ID='".$messageId."' AND METADATA_ID='".$routeId."' and CONTENT_TYPE='".$contentTypeId."'")->queryScalar();
        return $content;
    }
}
