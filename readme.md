# ITC Content Management System
[itcContentManagement](https://itc.ideateknoloji.com.tr)
# Listing
##### Paginate
```sh
    $data = MODEL::paginate(10)
```
You can http://xxx.com/model?page=1,2,3

##### Offset - Limit
```sh
    $offset = $request->offset ? $request->offset : 0;
    $limit = $request->limit ? $request->limit : 10;
    $data = Business::offset($offset)->limit($limit)->get();
```

##### Sorting
```sh
    ?sortBy=id&sort=DESC
```
# Create

if form and tables are same 
```sh
    $data = MODEL::create($request->all());
```
if not
```sh
        $data = new MODEL();
        $data->table1 = request('input1');
        $data->slug = Str::slug(request('title'));
        $data->integer = 2;
        $data->save();
        if ($data) {
            return $this->apiResponse(ResaultType::Success, $data, 'Content Created', 201);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'Content not saved', 500);
        }
```
# API CONTROLLER

controllerın yorumunu Controller yerine ApiController üzerinden çağırıp
```sh
class DATAController extends ApiController
```
```sh
    return $this->apiResponse(ResaultType::Success, $data, null, 201);
    return $this->apiResponse(ResaultType::Error, null, null, 500);
```



# Installation

PHP 7>
Laravel 6
Vue.js 2 (vue cli 3)
Bootstrap

```sh
$ cd www/itc
$ composer update
$ npm install -d
$ php artisan serve
```
