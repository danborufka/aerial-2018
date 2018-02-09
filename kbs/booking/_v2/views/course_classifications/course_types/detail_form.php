				<div class="title">
					<h2>Kursart- Details</h2>
				</div>
				<div id="button-bar">
					<div id="button-go-back" onclick="cm.CourseTypes.backToOverview();" class="rb-button">Zurück</div>
				</div>
				<form class="form-detail input-container clear-fix">
					<input type="hidden" name="id" id="ct_id" />
					<div class="clearfix">
						<div id="input-column1">
							<div class="input-row">
								<label for="ct_name">Kursartname*:</label>
								<input name="name" type="text" id="ct_name" >
							</div>
							<div class="input-row">
								<label for="ct_course_format">Kursformat*:</label>
								<select name='course_format_id' class="select_course_format" id='ct_course_format' >
									<option value=''>Loading...</option>
								</select>
							</div>
							<div class="input-row">
								<label for="ct_sort_no">Sortierung*:</label>
								<input name="sort_no" type="text" id="ct_sort_no" >
							</div>
							<div class="input-row">
								<label for="ct_is_kid_course">Kinderkurs*:</label>
								<select name='is_kid_course' id='ct_is_kid_course' >
									<option value='0'>nein</option>
									<option value='1'>ja</option>
								</select>
							</div>
							<div class="input-row">
								<label for="ct_payment_type">Bezahlung*:</label>
								<select name='payment_type' id='ct_payment_type' >
									<option value='1'>Banküberweisung</option>
									<option value='2'>bar</option>
								</select>
							</div>
							<div class="input-row">
								<label for="ct_status">Status*:</label>
								<select name='status' id='ct_status' class='select-with-symbols' >
									<option value='1'>✔   aktiv</option>
									<option value='0'>✖   deaktiviert</option>
								</select>
							</div>
						</div>
						
					</div>	
			    	<div id="button-bar2">
						<button class="button-save rb-button" onclick="cm.CourseTypes.save();" type="button" >Speichern</button>
						<button class="button-save-new rb-button" onclick="cm.CourseTypes.saveAndNew();" type="button" >Speichern und neu</button>
					</div>	
				</form>