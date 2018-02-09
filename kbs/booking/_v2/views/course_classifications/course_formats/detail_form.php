

			
				<div class="title">
					<h2>Kursformat- Details</h2>
				</div>
				<div id="button-bar">
					<div id="button-go-back" onclick="cm.CourseFormats.backToOverview();" class="rb-button">Zurück</div>
				</div>

				<form class="form-detail input-container clear-fix">
					<input type="hidden" name="id" id="cf_id" />
					<div class="clearfix">
						<div id="input-column1">
							<div class="input-row">
								<label for="cf_name">Kursformatname*:</label>
								<input name="name" type="text" id="cf_name" >
							</div>
							<div class="input-row">
								<label for="cf_sort_no">Sortierung*:</label>
								<input name="sort_no" type="text" id="cf_sort_no" >
							</div>

							<div class="input-row">
								
								<label for="cf_status">Status*:</label>
								<select name='status' id='cf_status' class='select-with-symbols' >
									<option value='1'>✔   aktiv</option>
									<option value='0'>✖   deaktiviert</option>
								</select>
							</div>
						</div>
					</div>	
			    	<div id="button-bar2">
						<button class="button-save rb-button" onclick="cm.CourseFormats.save();" type="button" >Speichern</button>
						<button class="button-save-new rb-button" onclick="cm.CourseFormats.saveAndNew();" type="button" >Speichern und neu</button>
					</div>	
				</form>