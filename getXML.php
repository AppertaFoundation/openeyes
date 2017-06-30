<?phpheader("Content-type: text/xml");$xml_output = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";$xml_output .= "<PatientList>	<Patient>
		<NHSNumber>0123456789</NHSNumber>
		<HospitalNumber>1009465</HospitalNumber>
		<Title>MRS</Title>
		<FirstName>Violet</FirstName>
		<Surname>Coffin</Surname>
		<DateOfBirth>1981-03-15</DateOfBirth>
		<Gender>F</Gender>
		<AddressList>
			<Address>
				<Line1>82 Scarisbrick Lane</Line1>
				<Line2/>
				<City>Bethersden</City>
				<County>West Yorkshire</County>
				<Postcode>QA88 2GC</Postcode>
				<Country>GB</Country>
				<Type>HOME</Type>
			</Address>
		</AddressList>
		<TelephoneNumber>03040 6024378</TelephoneNumber>
		<EthnicGroup>A</EthnicGroup>
		<DateOfDeath/>
		<PracticeCode>F001</PracticeCode>
		<GpCode>G0102926</GpCode>
	</Patient>
	<Patient>		<NHSNumber>0123456789</NHSNumber>		<HospitalNumber>22312423</HospitalNumber>		<Title>MRS</Title>		<FirstName>SECOND Violet</FirstName>		<Surname>Coffin</Surname>		<DateOfBirth>1978-03-01</DateOfBirth>		<Gender>F</Gender>		<AddressList>			<Address>				<Line1>82 Scarisbrick Lane</Line1>				<Line2/>				<City>Bethersden</City>				<County>West Yorkshire</County>				<Postcode>QA88 2GC</Postcode>				<Country>GB</Country>				<Type>HOME</Type>			</Address>		</AddressList>		<TelephoneNumber>03040 6024378</TelephoneNumber>		<EthnicGroup>A</EthnicGroup>		<DateOfDeath/>		<PracticeCode>F001</PracticeCode>		<GpCode>G0102926</GpCode>	</Patient>
	<Patient>		<NHSNumber>0123456789</NHSNumber>		<HospitalNumber>32312423</HospitalNumber>		<Title>MRS</Title>		<FirstName>THIRD Violet</FirstName>		<Surname>Coffin</Surname>		<DateOfBirth>1978-03-01</DateOfBirth>		<Gender>F</Gender>		<AddressList>			<Address>				<Line1>82 Scarisbrick Lane</Line1>				<Line2/>				<City>Bethersden</City>				<County>West Yorkshire</County>				<Postcode>QA88 2GC</Postcode>				<Country>GB</Country>				<Type>HOME</Type>			</Address>		</AddressList>		<TelephoneNumber>03040 6024378</TelephoneNumber>		<EthnicGroup>A</EthnicGroup>		<DateOfDeath/>		<PracticeCode>F001</PracticeCode>		<GpCode>G0102926</GpCode>	</Patient>
	</PatientList>";echo $xml_output;die;