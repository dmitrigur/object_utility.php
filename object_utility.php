function go_deeper($pcnt,$level,$object,$row,$key,$last_indx,$last_array) {
	if (isset($key[$level-1])) {
		if (gettype($key[$level-1])!='string' && (isset($key[$level-1]['key']) || isset($key[$level-1]['arr']))) {
			if (isset($key[$level-1]['par'])) {
				foreach ($key[$level-1]['par'] as $p=>$value) {
					if (!isset($object[$key[$level-1]['par'][$p]])) {
						if ($key[$level-1]['par'][$p]=='CNT') {
							$object['CNT']=1;
						} else if ($key[$level-1]['par'][$p]=='INDX') {
							$object['INDX']=$pcnt;
						} else {
							$object[$key[$level-1]['par'][$p]]=$row[$key[$level-1]['par'][$p]];
							unset($row[$key[$level-1]['par'][$p]]);
						}
					} else {
						if ($key[$level-1]['par'][$p]=='CNT') {
							$object['CNT']++;
						} else {
							unset($row[$key[$level-1]['par'][$p]]);
						}
					}
				}
			};
			if (isset($key[$level-1]['key'])) {
				$keyName=$key[$level-1]['key'];
				if (!isset($object[$keyName])) {
					$object[$keyName]=json_decode('{}',true);
				};
				$lkey=$row[$keyName];
				unset($row[$keyName]);
				if (count($row)>0) {
					if (isset($lkey)) {
						if (!isset($object[$keyName][$lkey])) {
							$object[$keyName][$lkey]=Array();
						};
						$object[$keyName][$lkey]=go_deeper(count($object[$keyName])-1,$level+1,$object[$keyName][$lkey],$row,$key,$last_indx,$last_array);
					}
				} else if ($last_indx) {
					$object[$keyName][$lkey]=Array('INDX'=>count($object[$keyName]));
				} else {
					$object[$keyName][]=$lkey;
				}
			} else {
				$keyName=$key[$level-1]['arr'];
				if (!isset($object[$keyName])) {
					$object[$keyName]=Array();
				};
				$lkey=$row[$keyName];
				unset($row[$keyName]);
				if (count($row)>0) {
					if (isset($lkey)) {
						$object[$keyName]=go_deeper(count($object[$keyName])-1,$level+1,$object[$keyName],$row,$key,$last_indx,$last_array);
					}
				} else if ($last_indx) {
					$object[$keyName][$lkey]=Array('INDX'=>count($object[$keyName]));
				} else {
					$object[$keyName][]=$lkey;
				}
			}
		} else {
			$lkey=$row[$key[($level-1)]];
			unset($row[$key[($level-1)]]);
			if (count($row)>0) { 
				if (isset($lkey)) {
					if (!isset($object[$lkey])) {
						$object[$lkey]=Array();
					};
					$object[$lkey]=go_deeper(count($object)-1,$level+1,$object[$lkey],$row,$key,$last_indx,$last_array);
				}
			} else if ($last_indx) {
				$object[$lkey]=Array('INDX'=>count($object));
			} else {
				$object[]=$lkey;
			}
		}
	} else {
		if ($object==Array()) {
			$cnt=count($row);
			if ($cnt==1) {
				foreach ($row as $key=>$value) {
					$row=$row[$key];
				}
			};
			if ($last_indx) {
				if ($last_array) {
					$object['VALUES']=Array($row);
					$object['INDX']=$pcnt;
			} else if ($cnt==1) {
					$object['VALUES']=$row;
					$object['INDX']=$pcnt;
				} else {
					$object=$row;
					$object['INDX']=$pcnt;
				}
			} else {
				if ($last_array) {
					$object=Array($row);
				} else {
					$object=$row;
				}
			};
		} else if (isset($object['VALUES']) && isset($object['VALUES'][0])) {
			if (count($row)==1) {
				foreach ($row as $key=>$value) {
					$row=$row[$key];
				};
			};
			$object['VALUES'][]=$row;
		} else if (isset($object[0])) {
			if (count($row)==1) {
				foreach ($row as $key=>$value) {
					$row=$row[$key];
				};
			};
			$object[]=$row;
			} else if ($last_indx) {
			if (count($row)==1) {
				foreach ($row as $key=>$value) {
					$row=$row[$key];
				};
				$object=Array('INDX'=>$object['INDX'],'VALUES'=>Array($object['VALUES'],$row));
			} else {
				$object=Array('INDX'=>$object['INDX'],'VALUES'=>Array($object,$row));
			}
			unset($object['VALUES'][0]['INDX']);
		} else {
			if (count($row)==1) {
				foreach ($row as $key=>$value) {
					$row=$row[$key];
				};
			};
			$object=Array($object,$row);
		};
	}
	return $object;
};

function pdo_select_tree_object($db_link2,$sql_str,$placeholders=Array(),$key=Array(),$last_indx=false,$last_array=false) {
	if ($stmt=$db_link2->prepare($sql_str)) {
		$stmt->execute($placeholders);
		$object=Array();
		while ($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
			$object=go_deeper(0,1,$object,$row,$key,$last_indx,$last_array);
		};
		return $object;
	} else {
		return false;
	}
};
