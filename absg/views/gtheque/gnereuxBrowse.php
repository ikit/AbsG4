
	<div id="forumHeaderSlot">
		<h1 id="gthequeHeader" class="topTitle">
			<a class="pathRoot"><span>Accueil</span></a>
			<?php foreach ($path as $pahtItem) : ?>
			 > <a href="<?php echo $pahtItem['url']; ?>" title="Accéder à <?php echo $pahtItem['title']; ?>"><?php echo $pahtItem['title']; ?></a>
			<?php endforeach; ?>
		</h1>
	</div>

	<br class="clear"/>

	<h2>Ma liste :</h2>
	<tableid="wishesList" class="table table-striped table-bordered" cellspacing="0" width="100%">
		<thead>
			<tr>
				<th>Souhait</th>
				<th>Détails</th>
				<th>Tags</th>
				<th></th>
			</tr>
		</thead>

		<?php foreach ($wishes as $elmt) : ?>
		<tr>
			<td><?php echo $elmt->title; ?></td>
			<td><?php echo $elmt->details; ?></td>
			<td><?php echo $elmt->tags; ?></td>
			<td>[Delete] [Edit]</td>
		</tr>
		<?php endforeach; ?>
	</table>

	<h2>Souhaits des autres :</h2>
	<tableid="othersList" class="table table-striped table-bordered" cellspacing="0" width="100%">
		<thead>
			<tr>
				<th>Bienheureux</th>
				<th>Souhait</th>
				<th>Détails</th>
				<th>Tags</th>
				<th>Action</th>
				<th>Commentaires</th>
			</tr>
		</thead>

		<?php foreach ($others as $elmt) : ?>
		<tr>
			<td><?php echo $elmt->avatar; ?><?php echo $elmt->username; ?></td>
			<td><?php echo $elmt->title; ?></td>
			<td><?php echo $elmt->details; ?></td>
			<td><?php echo $elmt->tags; ?></td>
			<td><?php echo $elmt->donor; ?></td>
			<td><?php echo $elmt->donors_chat; ?></td>
		</tr>
		<?php endforeach; ?>
	</table>


	

	<script type="text/javascript">
    // <![CDATA[

        $(document).ready(function()
        {
       		

        });
    // ]]> 
    </script>