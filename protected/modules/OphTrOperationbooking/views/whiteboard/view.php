<html>
<head>
    <style type='text/css'>
        * {
            font: 16pt arial, sans-serif;
            color: black;
        }

        b {
            color: black;
        }

        td p {
            color: white;
        }

        td {
            color: white;
            background-color: #bdd7ee;
            border-radius: 10px;
            padding: 10px;
        }

        td.highlight {
            background-color: #f8cbad !important;
        }
    </style>
</head>

<body>
<div style="float:right"><a href="javascript:void(0)" onclick="window.close()">Close</a></div>

<table width='100%' cellpadding='5' cellspacing='10'>
    <tr valign='top'>
        <td>Patient details<p>
                <b><?php echo $data['patientName'] ?>
                    <br/><?php echo $data['dob'] ?>
                    <br/><?php echo $data['hos_num'] ?></b>
        </td>
        <td>Operation Side<p>
                <b><?php echo $data['eyeSide'] ?></b>
        </td>
        <td>Operation Type<p>
                <b><?php echo $data['procedure'] ?></b>
        </td>
    </tr>
    <tr valign='top'>
        <td class='highlight'>IOL Model<p>
                <b><?php echo $data['iol_model'] ?></b>
        </td>
        <td class='highlight'>IOL Power<p>
                <b><?php echo $data['iol_power'] ?></b>
        </td>
        <td>Predicted refractive outcome<p>
                <b><?php echo $data['predictedRefractiveOutcome'] ?></b>
        </td>
    </tr>
    <tr valign='top'>
        <td class='highlight'>Allergies<p>
                <b><?php echo nl2br($data['allergies']) ?></b>
        </td>
        <td>Alpha-blockers<p>
                <b><?php echo $data['alphaBlockers'] ?></b>
        </td>
        <td>Predicted additional equipment<p>
                <b><?php echo nl2br($data['predictedAdditionalEquipment']) ?></b>
        </td>
    </tr>
    <tr>
        <td>Anticoagulants<p>
                <b><?php echo $data['anticoagulants'] ?></b>
            <p>INR
            <p>
                <b><?php echo $data['inr'] ?></b>
        </td>
        <td colspan='2'>Comments<p>
                <b><?php echo nl2br($data['comments']) ?></b>
        </td>
    </tr>
</table>

</body>
</html>