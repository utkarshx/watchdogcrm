<?php
return array(
	'name'=>"Template",
	'install'=>array(
		'CREATE TABLE x2_templates(
		id INT NOT NULL AUTO_INCREMENT primary key,
		assignedTo VARCHAR(250),
		name VARCHAR(250) NOT NULL,
		description TEXT,
		createDate INT,
		lastUpdated INT,
		updatedBy VARCHAR(250)
		)',
		'INSERT INTO x2_fields (modelName, fieldName, attributeLabel, custom, type) VALUES 
		("Templates", "id", "ID", "0", "int"),
		("Templates", "name", "Name", "0", "varchar"),
		("Templates", "assignedTo", "Assigned To", "0", "assignment"),
		("Templates", "description", "Description", "0", "text"),
		("Templates", "createDate", "Create Date", "0", "date"),
		("Templates", "lastUpdated", "Last Updated", "0", "date"),
		("Templates", "updatedBy", "Updated By", "0", "assignment")'
    ),
	'uninstall'=>array(
		'DELETE FROM x2_fields WHERE modelName="Templates"',
		'DROP TABLE x2_templates',
	),
	'editable'=>true,
	'searchable'=>true,
	'adminOnly'=>false,
	'custom'=>true,
	'toggleable'=>true,
);
?>
