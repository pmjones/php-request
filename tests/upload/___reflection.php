<?php
echo preg_replace('/\?(\w+)/', '$1 or NULL', (new ReflectionClass(SapiUpload::CLASS)));
var_dump(new SapiUpload(null, null, null, null, null));
