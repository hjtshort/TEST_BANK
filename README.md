<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>

## Framework sử dụng: Laravel

- Version 8.36.2
- Php version: 7.4

## Giải thích biến trong config file(.env)

    DB_CONNECTION: loại database kết nối (mysql)
    DB_HOST: database host IP
    DB_PORT: database port
    DB_DATABASE: tên database
    DB_USERNAME: username của database
    DB_PASSWORD: password của database
    QUEUE_CONNECTION: loại của queue job (database)

## Cách run project

    Copy .env.example thành .env
    Run command: composer install
    Run command: php artisan key:genrate
    Run command: php artisan serve
    Run command: php artisan db:seed
    Run command: php artisan queu:work --timeout=300  

### Tài khoản đăng nhập:

    Sau khi run các command ở phần Cách run project chúng ta có sẵn 1 user với:
    - ID(email): admin@gmail.com
    - Password: password

### Các thư viện dùng

- **[Laravel Excel](https://laravel-excel.com/)**
- **[Laravel Sanctum](https://laravel.com/docs/8.x/sanctum)**

# Giải thích api url

### Đăng nhập:

    - Url: {{domain}}/api/login
    - Method: post
    - Headers:
        - Accept: application/json
    - Form data:
        - email: trường email trong database
        - password: trường password trong database, mặc định là password
    - Response:
        - Thành công:
            - token
            - status: 200
        - Thất bại
            - message
            - errors

    -Full curl: 
        curl --location --request POST 'http://localhost:8000/api/login' \
            --header 'Accept: application/json' \
            --header 'Content-Type: application/json' \
            --data-raw '{
            "email" : "admin@gmail.com",
            "password": "password"
            }'

### Lấy thông tin đăng nhập

    - Url: {{domain}}/api/user
    - Method: get
    - Headers:
      - Accept: application/json
      - Authorization: Bearer + token
    - Response: 
        - Thành công: 
            - user: chứa thông tin người  dùng đăng nhập
            - status: 200
        - Thất bại:
            - message: "Unauthenticated"
            - status: 401

    - Full curl:
        curl --location --request GET 'http://localhost:8000/api/user' \
        --header 'Accept: application/json' \
        --header 'Authorization: Bearer {{token}}'

### Upload file excel để import

    - Url: {{domain}}/api/import
    - Method: post
    - Headers: 
        - Accept: application/json
        - Authorization: Bearer + token
        - Content-type: multipart/form-data
    - Form data:
        - file: Kiểu file (xlsx,csv)
    - Response:
        - Thành công:
            - message: "Upload success"
            - status: 201
        - Thất bại:
            - Lỗi validation 
                - status: 422
                - message: "The given data was invalid."
                - errors: 
                    - file: lỗi file upload
            - Lỗi khác
                - status: 400
                - message: "Cannot upload file"

    - Full curl: 
        curl --location --request POST 'http://localhost:8000/api/import' \
            --header 'Accept: application/json' \
            --header 'Authorization: Bearer {{token}}' \
            --form 'file=@{{file}}'

### Lấy danh sách file đã upload của user đã đăng nhập

    - Url: {{domain}}/api/user/file-imported
    - Method: get
    - Headers: 
        - Accept: application/json
        - Authorization: Bearer + token
    - Query params:
        - type: 
            - Nếu để trống lấy danh sách các file đã upload
            - Nếu type=1: lấy danh sách các file đã import thành công
            - Nếu type=2: lấy danh sách các file đã import có các record lỗi
            - Nếu type=3: Lấy danh sách các file đã upload đã upload nhưng đang chờ queue import
        - page (phân trang): 
            - Nếu để trống thì page bằng 1
            - Nếu muốn xem trang 2 thì thếm page bằng 2
    - Response:
        - Thành công:
            - data: chứa danh sách file
            - meta: chưa thông tin phân trang (pagination)
            - status: 

    - Full curl: 
        curl --location --request GET 'http://localhost:8000/api/user/file-imported?page=1' \
        --header 'Accept: application/json' \
        --header 'Authorization: Bearer {{token}}'

### Lấy danh sách record import bị lỗi của user đã đăng nhập

    - Url: {{domain}}/api/user/file-imported/{file_id}/fails
    - Method: get
    - Url Params:
        - file_id: id file của user đã đăng nhập
    - Headers: 
        - Accept: application/json
        - Authorization: Bearer + token
    - Query Params: 
        - page (phân trang): 
            - Nếu để trống thì page bằng 1
            - Nếu muốn xem trang 2 thì thếm page bằng 2
    - Response:
        - Thành công:
            - data: chứa danh sách file
            - meta: chưa thông tin phân trang (pagination)
            - status: 200
        - Thất bại: 
            - Lỗi file_id không tồn tại: 
                - status: 404

    - Full curl: 
        curl --location --request GET 'http://localhost:8000/api/user/file-imported/{{file_id}}/fails?page=1' \
        --header 'Accept: application/json' \
        --header 'Authorization: Bearer {{token}}'

### Lấy danh sách tất cả record đã import

    - Url: {{domain}}/api/transaction
    - Method: get
    - Headers: 
        - Accept: application/json
        - Authorization: Bearer + token
    - Query Params: 
        - page (phân trang): 
            - Nếu để trống thì page bằng 1
            - Nếu muốn xem trang 2 thì thếm page bằng 2
    - Response:
        - Thành công:
            - data: chứa danh sách file
            - meta: chưa thông tin phân trang (pagination)
            - status: 200

    - Full curl: 
        curl --location --request GET 'http://localhost:8000/api/transaction?page=1' \
            --header 'Accept: application/json' \
            --header 'Authorization: Bearer {{token}}'

Chi tiết vui lòng tham khảo tại [Bank api](https://documenter.getpostman.com/view/4476561/TzCV3QSX)

## Mô tả

    - Người dùng đăng nhập và upload file excel với định dạng xlsx hoặc file csv. Hệ thống lưu lại file, đưa vào queue job.
    - Mỗi lần job chạy sẽ đọc 10000 dòng của file excel và insert vào database.
    - Mỗi câu insert chứa 500 dòng. Các dòng nào bị bắt lỗi không đúng định dạng sẽ bị bỏ qua và lưu vào bảng transaction bị lỗi (transaction_fails).
    - Nếu file excel lớn hơn 10000 dòng. thì sau khi đọc 10000 dòng đầu tiên hệ thống sẽ tạo thêm 1 job mới và dispatch vào queue để đọc và lưu 10000 dòng tiếp theo. công việc sẽ lập lại đến khi đọc hết file.
    - Nếu file có bất kỳ dòng lỗi nào thì state của file sẽ bằng 2(có dòng lỗi). ngược lại state của file sẽ bằng 1. Nếu state bằng 0 tức file đang trong hệ thống queu chờ để import (bảng file_imports).

## Cấu trúc thư mục

    - File routes/api.php chứa các router của ứng dụng
    - Mỗi router sẽ thực thi 1 function trong controller đã cấu hình trong router. [tên controller, têm hàm thực thi] 
    - Các controller nằm trong folder app/Http/Controllers
    - Các file request validation nằm trong folder app/Http/Requests
    - File xử lý Import dữ liệu là app/Http/Import/TransactioImport

## Gợi ý

    - Vì file nặng và hệ thống xử lý lâu nên có thể phải thay đổi cái biết init của php
        - max_execution_time = 180
        - max_input_time = 120
        - upload_max_filesize = 100M   
        - post_max_size = 200M
        - memory_limit = 4096M  

Hướng dẫn cấu hình(https://www.rixosys.com/how-to-change-the-maximum-execution-time-for-php-project/)

## Unit test

    Folder chứa code: tests/Feature/UserTest.php
        -  tên function test_ + với tên hiển thị ở ngoài tương ứng. VD: can login thì tên function chứa code test là test_can_login.
    Run command: php artisan test
    Note: sau khi run test database tự động refresh
    - can login: test người dùng có thể login và trả về token
    - cannot login: test người dùng nhập sai tài khoản hoặc mật khẩu
    - cannot upload without token: test người dùng có thể upload file để import khi không có token chứng thực
    - cannot upload without file: test không thể úp load khi không có biến file kiểu file trong form data. trả về validation error
    - cannot upload file not excel: test người dùng không thể upload khi file không phải dịnh đạng xlsx, csv
    - can upload but not import because malformed: test không thể import khi file không đúng định dạng mẫu
    - cannot get list transaction without token: test người dùng không thể lấy danh sách transaction đã import khi không có token chứng thực
    - can get list transaction: test người dùng có thể lấy danh sách transaction đã import khi có token và chứng thực thành công
    - cannot get file imported without token: test người dùng không thể lấy danh sách file đã import khi không có token
    -  can get file imported: test người dùng có thể lấy danh sách file đã import khi có token và chứng thực thành công
    - cannot get list transaction fail without token: test người dùng không thể lấy danh sách transaction lỗi khi không có token    
    - cannot get list transaction fail with id not found: test người dùng không thể lấy danh sách transaction lỗi khi sai url hoặc file id không đúng
    - can get list transaction fail: test người dùng không thể lấy danh sách transaction lỗi khi có token chứng thực thành công và đúng url
