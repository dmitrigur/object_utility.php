This is functions that derived a table from database according to given SQ and transforms(parse) it in into and multi-level array according to provided ruling command.
This helps a developer, by avoiding to run multilevel parsing cycles.

Documentation:

pdo_select_tree_object($db_link2 object ,$sql_str string,[$placeholders = Array() array or assoc array,[$key =Array() Array,[$last_indx=false bool,[$last_array=false bool]]]]) return Array()

where 
$db_link2 - established link to database
$sql_str - structural query with placeholders 
$placeholders - array of values for placeholders
$key - rule of parsins command
$last_indx - true if yoou need 'INDX' property for last level.
$last_array

Sample

format of columns of SQ
'employee_id','employee_name','email','project_id','project_name','date_of_creation','department','coworkers_id','coworkers_name','coworkers_role'

and we need to get following structure of array
{employee_id: {
                            employee_name,
                            email,
                            project_id: {
                                                 project_name,
                                                 date_of_creation,
                                                 department,
                                                 coworkers_id: [
                                                                        1:{
                                                                              coworkers_name,
                                                                              coworkers_role
                                                                           },
                                                                   ]
                                               }
                           }
}
For this case we need to create following ruling array:                         
$Rule=Array(
         'employee_id',
         Array(
                   'par'=>Array('employee_name','email'),
                   'key'=>'project_id',
         ),
         Array(
                  'par'=>Array('project_name','date_of_creation','department'),
                  'arr'=>'coworkers_id'
         )
)
and ran the function with following  params:

pdo_select_tree_object($db_link2 ,$sql_str,$placeholders,$Rule) function returns built array with above configuration.

