<section>
    <h1>Part 1: Certificate of Vision Impairment</h1>
    <h2>Patient's details</h2>
    <table class="visually-impaired-table">
        <tbody>
            <tr>
                <td stlye="width:50%;">Title and surname or family name</td>
                <td stlye="width:50%;"><?php echo CHtml::encode($elements['Title_Surname']) ?></td>
            </tr>
            <tr>
                <td stlye="width:50%;">All other names(identify preferred name)</td>
                <td stlye="width:50%;"><?php echo CHtml::encode($elements['All_other_names']) ?></td>
            </tr>
            <tr>
                <td stlye="width:50%;">Address<br>(including postcode)</td>
                <td stlye="width:50%;"><?php echo CHtml::encode($elements['Address1']) ?><br><?php echo CHtml::encode($elements['Address2']) ?></td>
            </tr>
            <tr>
                <td stlye="width:50%;">Telephone number</td>
                <td stlye="width:50%;"><?php echo CHtml::encode($elements['Telephone']) ?></td>
            </tr>
            <tr>
                <td stlye="width:50%;">Email address</td>
                <td stlye="width:50%;"><?php echo CHtml::encode($elements['Email']) ?></td>
            </tr>
            <tr>
                <td stlye="width:50%;">Date of Birth<br>(dd/mm/yyyy)</td>
                <td stlye="width:50%;"><?php echo CHtml::encode(date("d/m/Y", strtotime($elements['dob_original']))) ?></td>
            </tr>
            <tr>
                <td stlye="width:50%;">Sex</td>
                <td stlye="width:50%;"><?php echo CHtml::encode($elements['Sex_String']) ?></td>
            </tr>
            <tr>
                <td stlye="width:50%;">NHS Number</td>
                <td stlye="width:50%;"><?php echo CHtml::encode($elements['NHS_1']).' '.CHtml::encode($elements['NHS_2']).' '.CHtml::encode($elements['NHS_3']) ?></td>
            </tr>
        </tbody>
    </table>
</section>
<section>
    <h1>To be completed by the Ophthalmologist</h1>
    <div class="border">
        <p>Tick the box that applies</p>
        <p><strong>I consider that</strong></p>
        <p>
            <?php if ($elements['Opthalm1'] == 0) { ?>
            <img class="tickbox" src="<?php echo CHtml::encode($imageFolder).'box_ticked.png' ?>" />    
            <?php } else { ?>  
            <img class="tickbox" src="<?php echo CHtml::encode($imageFolder).'box_unticked.png' ?>" />    
            <?php } ?>
            <strong>This person is sight impaired (partially sighted)</strong>
        </p>
        <p>
            <?php if ($elements['Opthalm1'] == 0) { ?>
            <img class="tickbox" src="<?php echo CHtml::encode($imageFolder).'box_unticked.png' ?>" />   
            <?php } else { ?>  
            <img class="tickbox" src="<?php echo CHtml::encode($imageFolder).'box_ticked.png' ?>" />    
            <?php } ?>
            <strong>This person is severely sight impaired (blind)</strong>
        </p>
        <p>
            I have made the patient aware of the 
        </p>
    </div>
    <div class="border">
        <p>
            information booklet, <br>
            “Sight Loss: What we needed to know”<br>
            (www.rnib.org.uk/sightlossinfo)<br>
            <strong><?php echo CHtml::encode($elements['information_booklet_string']); ?></strong>
        </p>
        <p>
            Has the patient seen an Eye Clinic Liaison Officer (ECLO)/Sight Loss Advisor?  
            <strong><?php echo CHtml::encode($elements['sight_loss_advisior']); ?></strong>
        </p>
        <p>
            <strong>Signed</strong><img class="consultantSignatureImg" src="<?php echo CHtml::encode($elements['consultantSignatureImgSrc']); ?>"/>
            <strong>Date of examination</strong><br><?php echo CHtml::encode($elements['Examination_date']); ?><br>
        </p>
        <p>
            <strong>Name <?php echo CHtml::encode($elements['Opth_Name']) ?></strong>
        </p>
        <p>
            <strong>Hospital address</strong><br>
            <?php echo CHtml::encode($elements['Hospital_address1']); ?><br>
            <?php echo CHtml::encode($elements['Hospital_address2']); ?><br>
        </p>
        <p>
            NB: the date of examination is taken as the date from which any concessions are calculated
        </p>
    </div>
    <div>
        <strong>
            For Hospital staff: Provide/send copies of this CVI as stated below
        </strong>
        <p>
            <img class="tickbox" src="<?php echo CHtml::encode($imageFolder).'box_unticked.png' ?>" /> An accessible signed copy of the CVI form to the patient (or parent/guardian if the patient is a child). 
        </p>
        <p>
            <?php if ($elements['Pages 1-5 to the patient’s local council if the patient (or parent/guardian if the patient is a chil'] == "Yes") { ?>
               <img class="tickbox" src="<?php echo CHtml::encode($imageFolder).'box_ticked.png' ?>" /> 
            <?php } else { ?>
               <img class="tickbox" src="<?php echo CHtml::encode($imageFolder).'box_unticked.png' ?>" /> 
            <?php } ?>
               Pages 1-11 to the patient’s local council if the patient (or parent/guardian if the patient is a child) consents, <strong>within 5 working days.</strong> 
        </p>
        <p>
            <?php if ($elements['Pages 1-5 to the patient’s GP, if the patient (or parent/guardian if the patient is a child) consent'] == "Yes") { ?>
               <img class="tickbox" src="<?php echo CHtml::encode($imageFolder).'box_ticked.png' ?>" /> 
            <?php } else { ?>
               <img class="tickbox" src="<?php echo CHtml::encode($imageFolder).'box_unticked.png' ?>" /> 
            <?php } ?>
               Pages 1-11 to the patient’s GP, if the patient (or parent/guardian if the patient is a child) consents.
        </p>
        <p>
            <?php if ($elements['Pages 1-6 to The Royal College of Ophthalmologists'] == "Yes") { ?>
               <img class="tickbox" src="<?php echo CHtml::encode($imageFolder).'box_ticked.png' ?>" /> 
            <?php } else { ?>
               <img class="tickbox" src="<?php echo CHtml::encode($imageFolder).'box_unticked.png' ?>" /> 
            <?php } ?>
                Pages 1-13 to The Royal College of Ophthalmologists, c/o Certifications Office, Moorfields Eye           
                Hospital, 162 City Road, London, EC1V 2PD, or by nhs.net secure email to 
                meh-tr.CVI@nhs.net if the patient (or parent/guardian if the patient is a child) consents.

        </p>
    </div>
</section>

<section>
    <h1>Part 2: To be completed by the Ophthalmologist </h1>
    <h2>Visual function</h2>
    <table class="visually-impaired-table">
        <tr>
            <td>&nbsp;</td>
            <td>Right eye</td>
            <td>Left eye</td>
            <td>Binocular (Habitual)</td>
        </tr>
        <tr>
            <td><strong>Best corrected visual acuity</strong></td>
            <td>
                <?php
                if ($elements["Right eye: Logmar"] !== "") {
                    echo CHtml::encode($elements["Right eye: Logmar"]).'<br>Logmar';
                }
                if ($elements["Right eye: Snellen"] !== "") {
                    echo CHtml::encode($elements["Right eye: Snellen"]).'<br>Snellen';
                }
                ?>
            </td>
            <td>
                <?php
                if ($elements["Left eye: Logmar"] !== "") {
                    echo CHtml::encode($elements["Left eye: Logmar"]).'<br>Logmar';
                }
                if ($elements["Left eye: Snellen"] !== "") {
                    echo CHtml::encode($elements["Left eye: Snellen"]).'<br>Snellen';
                }
                ?>
            </td>
            <td>
                <?php
                if ($elements["Binocular: Logmar"] !== "") {
                    echo CHtml::encode($elements["Binocular: Logmar"]).'<br>Logmar';
                }
                if ($elements["Binocular: Snellen"] !== "") {
                    echo CHtml::encode($elements["Binocular: Snellen"]).'<br>Snellen';
                }
                ?>
            </td>
        </tr>
    </table>
    
    <table class="visually-impaired-table">
        <tr>
            <td>
                <strong>Field of vision</strong><br>
                Extensive loss of peripheral visual field   
                (including hemianopia)<br>  
                <?php if ($elements['Extensive loss of peripheral visual field (including hemianopia)'] == "0") { ?>
                    <strong>Yes</strong>
                <?php } else { ?>
                    <strong>No</strong>
                <?php } ?>
            </td>
            <td>
                <strong>Low vision service</strong><br>
                If appropriate, has a referral for the low vision service been made?<br>  
                <strong><?php echo CHtml::encode($elements['low_vision_service']); ?></strong>
            </td>
        </tr>
    </table>
</section>
<section>
    <h1>Part 2a: Diagnosis (for patients 18 years of age or <span class="underline">over</span>)</h1>
    <table class="visually-impaired-table" style="margin-top:10px;">
        <tr>
            <td colspan="2" style="width:57%;">Tick each box that applies. Circle the <strong>main</strong> cause where there is more than one</td>
            <td style="width:15%;">ICD 10 code</td>
            <td style="width:14%;">Right eye</td>
            <td style="width:14%;">Left eye</td>
        </tr>
        <?php echo CHtml::encode($elements['diagnosis_for_visualy_impaired']) ?>
    </table>
    <p>
        *Please note that this is not intended to be a comprehensive list of all possible diagnoses.
    </p>
</section>

<section>
    <h1>Part 3: To be completed by the patient (or parent/guardian if the patient is a child) and eye clinic staff e.g. ECLO/Sight Loss Advisor </h1>
    <h1>Additional information for the patient’s local council </h1>
    <p>
        If you are an adult do you live alone? <strong><?php echo ($elements['If you are an adult do you live alone?'] == 0) ? "Yes" : "No" ?></strong>
    </p>
    <p>  
        Does someone support you with your care?  <strong><?php echo ($elements['Does someone support you with your care?'] == 0) ? "Yes" : "No" ?></strong>
    </p>
    <p>   
        Do you have difficulties with your physical mobility?  <strong><?php echo ($elements['Do you have difficulties with your physical mobility?'] == 0) ? "Yes" : "No" ?></strong>
    </p>
    <p> 
        Do you have difficulties with your hearing?  <strong><?php echo ($elements['Do you have difficulties with your hearing?'] == 0) ? "Yes" : "No" ?></strong>
    </p>
    <p>   
        Do you have a learning disability?  <strong><?php echo ($elements['Do you have a learning disability?'] == 0) ? "Yes" : "No" ?></strong>
    </p>
    <p>  
        Do you have a diagnosis of dementia?  <strong><?php echo ($elements['Do you have a diagnosis of dementia?'] == 0) ? "Yes" : "No" ?></strong>
    </p>
    <p>    
        Are you employed?  <strong><?php echo ($elements['Are you employed?'] == 0) ? "Yes" : "No" ?></strong>
    </p>
    <p>   
        Are you in full-time education?  <strong><?php echo ($elements['Are you in full-time education?'] == 0) ? "Yes" : "No" ?></strong>
    </p>
    <p>
        If the patient is a baby, child or young person, is your child/are you known to the specialist visual impairment education service?  
        <strong>
            <?php
            if ($elements['If the patient is a baby, child or young person, is your child/are you known to the specialist visua'] == "0") {
                echo "Yes";
            } elseif ($elements['If the patient is a baby, child or young person, is your child/are you known to the specialist visua'] == "1") {
                echo "No";
            } else {
                echo "Don't know";
            }
            ?>
        </strong>
    </p>
    <table class="visually-impaired-table">
        <tr>
            <td>
                Record any further relevant information below e.g. medical conditions, emotional impact of sight loss, risk of falls, benefits of vision rehabilitation and/or if you think the patient requires urgent support and reasons why.<br><br>
                <?php echo CHtml::encode($elements['further_relevant_info']) ?>
            </td>
        </tr>
    </table>

    <h1>Patient’s information and communication needs</h1>
    <p>
        All providers of NHS and local authority social care services are legally required to identify, record and meet your individual information/communication needs (refer to Explanatory Notes paragraphs 9, 22 and 23). 
    </p>
    <table class="visually-impaired-table">
        <tr>
            <td style="width:50%;">Preferred method of contact telephone, email or letter?</td>
            <td style="width:50%;"><?php echo CHtml::encode($elements['Preferred_method_of_contact_string']); ?></td>
        </tr>
        <tr>
            <td>Preferred method of communication e.g. BSL, deafblind manual</td>
            <td><?php echo CHtml::encode($elements['Pref_method']); ?></td>
        </tr>
        <tr>
            <td>Preferred format of information</td>
            <td><?php echo CHtml::encode($elements['Preferred_format_of_information_visualy_impaired']); ?></td>
        </tr>
        <tr>
            <td>Preferred language (and identify if an interpreter is required)</td>
            <td><?php echo CHtml::encode($elements['Pref_Language']); ?></td>
        </tr>
        
    </table>
</section>
<section>
    <h1>Part 4: Consent to share information I understand that by signing this form </h1>
    <?php if ($elements['Consent_to_GP'] == true) { ?>
        I give my permission for a copy to be sent to my GP to make them aware of this certificate.
    <?php } else { ?>
        I do not give my permission for a copy to be sent to my GP to make them aware of this certificate.
    <?php } ?>
        
    <table class="visually-impaired-table">
        <tr>
            <td style="width:50%;">My GP name/practice</td>
            <td style="width:50%;">
                <?php
                if ($elements['Consent_to_GP'] == true) {
                    echo CHtml::encode($elements["GP_name"]);
                }
                ?>
            </td>
        </tr>
        <tr>
            <td style="width:50%;">Address</td>
            <td style="width:50%;">
                <?php
                if ($elements['Consent_to_GP'] == true) {
                    echo CHtml::encode($elements["GP_Address"])."<br>".CHtml::encode($elements["GP_Address_Line_2"]);
                }
                ?>
            </td>
        </tr>
        <tr>
            <td style="width:50%;">Telephone number</td>
            <td style="width:50%;">
                <?php
                if ($elements['Consent_to_GP'] == true) {
                    echo CHtml::encode($elements["GP_Telephone"]);
                }
                ?>
            </td>
        </tr>
    </table>
        
    <?php if ($elements['Consent_to_Local_Council'] == true) { ?>
        I give my permission for a copy to be sent to my local council (or an organisation working on their behalf) who have a duty (under the Care Act 2014) to contact me to offer advice on living with sight loss and explain the benefits of being registered. When the council contacts me, I am aware that I do not have to accept any help, or be registered at that time, if I choose not to do so.
    <?php } else { ?>
        I do not give my permission for a copy to be sent to my local council (or an organisation working on their behalf) who have a duty (under the Care Act 2014) to contact me to offer advice on living with sight loss and explain the benefits of being registered. When the council contacts me, I am aware that I do not have to accept any help, or be registered at that time, if I choose not to do so.
    <?php } ?>
        
    <table class="visually-impaired-table">
        <tr>
            <td style="width:50%;">My local council name</td>
            <td style="width:50%;">
                <?php
                if ($elements['Consent_to_Local_Council'] == true) {
                    echo CHtml::encode($elements["Council_Name"]);
                }
                ?>
            </td>
        </tr>
        <tr>
            <td style="width:50%;">Address</td>
            <td style="width:50%;">
                <?php
                if ($elements['Consent_to_Local_Council'] == true) {
                    echo CHtml::encode($elements["Council_Address"])."<br>".CHtml::encode($elements["Council_Address2"]);
                }
                ?>
            </td>
        </tr>
        <tr>
            <td style="width:50%;">Telephone number</td>
            <td style="width:50%;">
                <?php
                if ($elements['Consent_to_Local_Council'] == true) {
                    echo CHtml::encode($elements["Council_Telephone"]);
                }
                ?>
            </td>
        </tr>
    </table>
    <?php if ($elements['Consent_to_RCO'] == true) { ?>
        I give my permission for a copy to be sent to The Royal College of Ophthalmologists. 
    <?php } else { ?>
        I do not give my permission for a copy to be sent to The Royal College of Ophthalmologists. 
    <?php } ?>
    I understand that I do not have to consent to sharing my information with my GP, local council or The Royal College of Ophthalmologists Certifications Office, or that I can withdraw my consent at any point by contacting them directly.
    I confirm that my attention has been drawn to the paragraph entitled ‘Driving’ on page 17 and understand that I must not drive.
    
    <table class="visually-impaired-table">
        <tr>
            <td style="width:35%;">Signed by the patient (or signature and name of parent/guardian or representative)</td>
            <td style="width:65%;">
                <div class="signed_by_text">
                    <?php echo nl2br(CHtml::encode($elements['signed_by'])); ?>
                </div>
                <img class="patientSignatureImg" src="<?php echo CHtml::encode($elements['patientSignatureImgSrc']); ?>"/>
            </td>
        </tr>
    </table>
</section>
<section>
    <h1>Ethnicity</h1>
    <p>(this information is needed for service and epidemiological monitoring)</p>
    <?php
    foreach ($elements['EthnicityForVisualyImpaired'] as $key => $ethnicity) {
        if ($elements["Ethnicity"] == $key ) { ?>
            <img class="tickbox" src="<?php echo CHtml::encode($imageFolder).'box_ticked.png' ?>" /> 
        <?php } else { ?>
            <img class="tickbox" src="<?php echo CHtml::encode($imageFolder).'box_unticked.png' ?>" /> 
        <?php }

        echo CHtml::encode($ethnicity['name']."<br>";

        if ($ethnicity['describe_needs'] == 1) { ?>
            <p class="describe_underline">
                <?php
                if ($key == 2) {
                    echo CHtml::encode($elements['Other White background description']);
                } elseif ($key == 6) {
                    echo CHtml::encode($elements['Ather Mixed/Multiple ethnic background description']);
                } elseif ($key == 10 ) {
                    echo CHtml::encode($elements['Other Asian background, description']);
                } elseif ($key == 13) {
                    echo CHtml::encode($elements['Other Black/African/Caribbean background description']);
                } elseif ($key == 15) {
                    echo CHtml::encode($elements['Other Chinese background description']);
                } elseif ($key == 16) {
                    echo CHtml::encode($elements['Other ethnicity description']);
                }
                ?>
            </p>
        <?php }
    }

    ?>
</section>
<section>
    <h1 class="underline">Information Sheet for patients (or parents/guardians if the patient is a child)</h1>
    <h1>Certification</h1>
    <p>
        Keep your Certificate of Vision Impairment (CVI). It has three main functions:
    </p>
    <p>
        1. It qualifies you to be registered with your local council as sight impaired (partially sighted) or severely sight impaired (blind).
    </p>
    <p>
        2. It lets your local council know about your sight loss. They should contact you within two weeks to offer registration, and to identify any help you might need with day-to-day tasks. 
    </p>
    <p>
        3. The CVI records important information about the causes of sight loss. It helps in planning NHS eye care services and research about eye conditions. 
    </p>
    <h2>Registration and vision rehabilitation/habilitation</h2>
    <p>
        Councils have a duty to keep a register of people with sight loss. They will contact you to talk about the benefits of being registered. This is likely to be through the Social Services Local Sensory Team (or an organisation working on 
        their behalf). Registration is often a positive step to help you to be as independent as possible. You can choose whether or not to be registered. Once registered, your local council should offer you a card confirming registration. 
        If you are registered, you may find it easier to prove the degree of your sight loss and your eligibility for certain concessions. The Council should also talk to you about vision rehabilitation if you are an adult, and habilitation 
        if you are a child or young person and any other support that might help. Vision rehabilitation/habilitation is support or training to help you to maximise your independence, such as moving around your home and getting out and about safely.
    </p>
    <h2>Early Years Development, Children and Young People and Education</h2>
    <p>Children (including babies) and young people who are vision impaired will require specialist support for their development and may receive special educational needs provision. An education, health and care (EHC) plan may be provided. You do not need to be certified or registered to receive this support or an EHC plan. This support is provided by the council’s specialist education vision impairment service. Additional support from a social care assessment may also be offered as a result of registration. Information about the support your council offers to children and young people can be found on the ‘Local Offer’ page of their website. If you or your child are not known to this service talk to the Ophthalmologist or ECLO/Sight Loss Advisor.</p>
    <h2>Driving</h2>
    <p>
        As a person certified as sight impaired or severely sight impaired you must not drive and you must inform the DVLA at the earliest opportunity. For more information, please contact: Drivers Medical Branch, DVLA, Swansea, SA99 1TU. Telephone 0300 790 6806. Email eftd@dvla.gsi.gov.uk
    </p>
    <h2>Where to get further information, advice and support</h2>
    <p>
        “Sight Loss: What we needed to know”, written by people with sight loss, contains lots of useful information including a list of other charities who may be able to help you. 
        Visit <br> www.rnib.org.uk/sightlossinfo
    </p>
    <p>
        ‘Sightline’ is an online directory of people, services and organisations that help people with sight loss in your area. 
        Visit <br> www.sightlinedirectory.org.uk
    </p>
    <p>
        ‘Starting Point’ signposts families to resources and professionals that can help with the first steps following your child’s diagnosis. 
        Visit <br> www.vision2020uk.org.uk/startingpoint
    </p>
    <p>
        Your local sight loss charity has lots of information, advice and practical solutions that can help you. Visit <br> www.visionary.org.uk
    </p>
    <p>
        RNIB offers practical and emotional support for everyone affected by sight loss. Call the Helpline on 0303 123 9999 or visit <br> www.rnib.org.uk
    </p>
    <p>
        Guide Dogs provides a range of support services to people of all ages. Call 0800 953 0113 (adults) or 0800 781 1444 (parents/guardians of children/young people) or visit <br> www.guidedogs.org.uk 
    </p>
    <p>
        Blind Veterans UK provides services and support to vision impaired veterans. Call 0800 389 7979 or visit <br> www.noonealone.org.uk
    </p>
    <p>
        SeeAbility is a charity that acts to make eye care more accessible for people with learning disabilities and autism. Their easy read information can be found at <br>
        www.seeability.org/lookingafter-your-eyes or you can call 01372 755000.
    </p>
</section>
