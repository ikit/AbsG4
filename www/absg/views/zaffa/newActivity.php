
	
	<form action="<?php echo base_url() . 'zaffa/addNewActivity' ?>" method="post" enctype="multipart/form-data">
		<label for="userId">Identifiant de l'utilisateur (1 = zaffa) :</label>
		<input name="userId" id="userId" />
		
		<label for="date">La date :</label>
		<input name="date" id="date" placeholder="aaaa,mm,jj,hh,min" />


		<label for="message">Le message (le plus court possible):</label>
		<input name="message" id="message" />


		<label for="url">L'url si nécessaire :</label>
		<input name="url" id="url" placeholder="grenier/updates/20131102" />


		<label for="type">Le type de message :</label>
		<select name="type" id="type">
			<option value="message">message</option>
			<option value="warning">warning</option>
			<option value="error">error</option>
		</select>


		<label for="module">Le module concerné par message :</label>
		<select name="module" id="module">
			<option value="absg">AbsG (en général)</option>
			<option value="citation"> - Citation</option>
			<option value="immt"> - Image du moment</option>
			<option value="forum"> - Forum</option>
			<option value="agpa"> - AGPA</option>
			<option value="agenda"> - Agenda</option>
			<option value="web3g"> - Web3G</option>
			<option value="cultureg"> - Culture G</option>
			<option value="gtheque"> - G-thèque</option>
			<option value="wikig"> - Wiki</option>
			<option value="olympiages"> - OlympiaGes</option>
			<option value="grenier"> - Grenier</option>
			<option value="birthday"> - Anniversaire</option>
		</select>

		<input type="submit" value="Enregistrer" />
	</form>
