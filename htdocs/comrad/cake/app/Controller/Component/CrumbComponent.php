<?php
class CrumbComponent extends Component {
	var $components = array('Session');
	
	public function saveCrumb($title, $request, $reset = false) {
		$crumbs = $this->Session->read('Breadcrumb.crumbs');
		
		if (!is_array($crumbs) || $reset) $crumbs = array();
		
		for ($i = 0; $i < count($crumbs); $i++) {
			if ($crumbs[$i]['controller'] === $request->params['controller'] && $crumbs[$i]['action'] === $request->params['action']) break;
		}
		
		if ($i < count($crumbs)) {
			$crumbs[$i]['title'] = $title;
			$crumbs[$i]['url'] = $request->here();
			array_splice($crumbs, $i + 1);
		} else {
			array_push($crumbs, array(
				'title' => $title,
				'controller' => $request->params['controller'],
				'action' => $request->params['action'],
				'url' => $request->here()
			));
		}
		$this->Session->write('Breadcrumb.crumbs', $crumbs);
	}
}
?>
