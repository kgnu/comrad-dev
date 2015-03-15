<?php

class PermissionManager extends Manager {
	private $cachedObjectParentArray = null;
	private $authenticationDisabled = false;
	private $authorizationDisabled = false;
	
	public static function getInstance() {
		static $instance;
		if (!isset($instance)) {
			$c = __CLASS__;
			$instance = new $c();
		}
		return $instance;
	}
	
	public function getObjectParentArray() {
		$this->cachedObjectParentArray = null;
		if (is_null($this->cachedObjectParentArray)) {
			$query = "SELECT o1.o_Name objectName, o2.o_Name parentName FROM DBObject o1
				LEFT JOIN DBObject o2 ON o1.o_ParentId = o2.o_Id";
		
			$queryResults = $this->doQuery($query);
		
			$this->cachedObjectParentArray = array();
			foreach ($queryResults as $queryResult) {
				$this->cachedObjectParentArray[$queryResult['objectName']] = $queryResult['parentName'];
			}
		}
		
		return $this->cachedObjectParentArray;
	}
	
	public function fetchPermissionsForRoleId($roleId) {
		$query = "SELECT op.*, o.* FROM DBObjectPermission op
			LEFT JOIN DBObject AS o ON op.op_DBObjectId = o.o_Id
			WHERE op.op_RoleId = ?";
		
		$params = new ParameterList();
		$params->add('i', '', $roleId);
		
		$queryResults = $this->doQuery($query, $params);
		
		$perms = array();
		foreach ($queryResults as $queryResult) {
			$perms[$queryResult['o_Name']] = array();
			$perms[$queryResult['o_Name']]['operations'] = array();
			if ($queryResult['op_Read']) array_push($perms[$queryResult['o_Name']]['operations'], 'read');
			if ($queryResult['op_Write']) array_push($perms[$queryResult['o_Name']]['operations'], 'write');
			if ($queryResult['op_Insert']) array_push($perms[$queryResult['o_Name']]['operations'], 'insert');
			if ($queryResult['op_Delete']) array_push($perms[$queryResult['o_Name']]['operations'], 'delete');
		}
		
		return array_merge($this->getDefaultPermissions(), $perms);
	}
	
	public function currentUserHasPermission($operation, $object, $property = null) {
		if ($this->authorizationDisabled) return true;
		
		$_SESSION['Permissions'] = null;
		
		if (!array_key_exists('Permissions', $_SESSION) || $_SESSION['Permissions'] == null) {
			$_SESSION['Permissions'] = $this->fetchPermissionsForRoleId($_SESSION['RoleId']);
		}
		
		$objectParentArray = $this->getObjectParentArray();
		
		do {
			if (!array_key_exists($object, $_SESSION['Permissions'])) return false;
			if (!array_key_exists('operations', $_SESSION['Permissions'][$object])) return false;
			if (!in_array($operation, $_SESSION['Permissions'][$object]['operations'])) return false;
			$object = (array_key_exists($object, $objectParentArray) ? $objectParentArray[$object] : null);
			if ($object != null && !array_key_exists($object, $objectParentArray)) return false;
		} while ($object != null);
		
		return true;
	}
	
	public function currentUserHasPermissions($operations, $objects) {
		if (!is_array($operations)) $operations = array($operations);
		if (!is_array($objects)) $objects = array($objects);
		
		foreach ($objects as $object) {
			foreach ($operations as $operation) {
				if (!$this->currentUserHasPermission($operation, $object)) return false;
			}
		}
		
		return true;
	}
	
	public function getDefaultPermissions() {
		$query = "SELECT o.o_Name FROM DBObject o";
		
		$queryResults = $this->doQuery($query);
		
		$defaultPerms = array();
		foreach ($queryResults as $queryResult) {
			$defaultPerms[$queryResult['o_Name']] = array();
			$defaultPerms[$queryResult['o_Name']]['operations'] = array();
		}
		
		return $defaultPerms;
	}
	
	public function disableAuthentication() {
		$this->authenticationDisabled = true;
	}
	
	public function enableAuthentication() {
		$this->authenticationDisabled = false;
	}
	
	public function isAuthenticationDisabled() {
		return $this->authenticationDisabled;
	}
	
	public function disableAuthorization() {
		$this->authorizationDisabled = true;
	}
	
	public function enableAuthorization() {
		$this->authorizationDisabled = false;
	}
}

?>