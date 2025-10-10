# 와일드베리스 주문 관리 시스템 (WB-OMS)

러시아 이커머스 플랫폼 'Wildberries' 연동 주문 관리 시스템입니다.

## 기술 스택

- **Backend**: Laravel 12
- **Admin Panel**: Filament v3
- **Database**: PostgreSQL (또는 MySQL)
- **Queue**: Laravel Queue
- **Authentication**: Filament 내장 인증
- **Permission**: Spatie Laravel Permission + Filament Shield

## 주요 기능

### 1. 주문 관리
- 와일드베리스 API를 통한 자동 주문 수집 (15분마다)
- 러시아어 → 한국어/영어 자동 번역
- 주문 상태 추적 (대기중, 번역완료, WMS전송완료, 배송중, 완료, 취소)
- 상세 필터 및 검색 기능

### 2. 상품 관리
- 상품 정보 관리
- 재고 수량 추적
- 와일드베리스 상품 ID 연동

### 3. 배송 관리 (WMS)
- 외부 WMS와 자동 연동
- 배송 요청 및 상태 추적
- 송장 번호 관리

### 4. API 로그
- 모든 외부 API 호출 기록
- 요청/응답 데이터 저장
- 에러 추적 및 성능 모니터링

### 5. 대시보드
- 실시간 통계 (일/주/월 주문 및 매출)
- 평균 주문 금액
- 차트 및 통계 위젯

## 설치 방법

### 1. 환경 설정

```bash
# 저장소 클론 (이미 클론되어 있는 경우 건너뛰기)
git clone <repository-url>
cd wb-oms

# 의존성 설치
composer install
npm install

# 환경 변수 파일 생성
cp .env.example .env

# 애플리케이션 키 생성
php artisan key:generate
```

### 2. 데이터베이스 설정

`.env` 파일에서 데이터베이스 설정:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=wb_oms
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

마이그레이션 실행:

```bash
php artisan migrate
```

### 3. API 설정

`.env` 파일에 API 키 설정:

```env
# Wildberries API
WILDBERRIES_API_KEY=your_wildberries_api_key
WILDBERRIES_BASE_URL=https://suppliers-api.wildberries.ru

# WMS API
WMS_API_KEY=your_wms_api_key
WMS_BASE_URL=https://your-wms-api.com

# Translation API (google, deepl, yandex 중 선택)
TRANSLATION_PROVIDER=google
TRANSLATION_API_KEY=your_translation_api_key
```

### 4. 관리자 계정 생성

```bash
php artisan make:filament-user
```

또는 이미 생성된 계정 사용:
- Email: admin@example.com
- Password: Demo@WB2025!
- **참고**: 로그인 페이지에서 데모 계정 정보가 자동으로 입력됩니다.

### 5. 프론트엔드 빌드

```bash
npm run build
# 또는 개발 모드
npm run dev
```

### 6. 큐 워커 실행

```bash
php artisan queue:work
```

### 7. 스케줄러 설정

Cron 설정 추가:

```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

## 실행

### 개발 환경

```bash
php artisan serve
```

관리자 패널 접속: http://localhost:8000/admin

### 프로덕션 환경

웹 서버(Nginx, Apache 등) 설정 후:
- Document Root: `/public`
- 관리자 패널: https://yourdomain.com/admin

## 주요 작업 스케줄

- **15분마다**: 와일드베리스 신규 주문 수집
- **주문 수집 시**: 자동 번역 작업 큐에 추가
- **수동**: WMS 배송 요청 (관리자가 Filament에서 실행)

## 파일 구조

```
app/
├── Filament/
│   ├── Resources/          # Filament 리소스
│   └── Widgets/            # 대시보드 위젯
├── Jobs/                   # Queue 작업
│   ├── FetchWildberriesOrders.php
│   ├── TranslateOrderData.php
│   └── SendWmsShipmentRequest.php
├── Models/                 # Eloquent 모델
│   ├── Order.php
│   ├── OrderItem.php
│   ├── Product.php
│   ├── WmsShipment.php
│   └── ApiLog.php
└── Services/               # 외부 API 서비스
    ├── WildberriesService.php
    ├── WmsService.php
    └── TranslationService.php
```

## 사용 방법

### 주문 처리 흐름

1. **자동 수집**: 스케줄러가 15분마다 와일드베리스 주문 수집
2. **자동 번역**: 수집된 주문의 고객 정보 자동 번역
3. **수동 확인**: 관리자가 Filament에서 번역된 주문 확인
4. **WMS 전송**: 확인된 주문을 WMS로 배송 요청
5. **배송 추적**: WMS에서 배송 상태 업데이트

### 권한 관리

Filament Shield를 통해 역할 기반 권한 관리:

```bash
# 권한 생성
php artisan shield:generate
```

## 개발

### 코드 스타일

```bash
# Pint 실행 (자동 포맷팅)
vendor/bin/pint
```

### 테스트

```bash
# 모든 테스트 실행
php artisan test

# 특정 파일 테스트
php artisan test tests/Feature/OrderTest.php
```

## 문제 해결

### 큐 작업이 실행되지 않는 경우

```bash
# 큐 워커 재시작
php artisan queue:restart
php artisan queue:work
```

### 캐시 문제

```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
```

### 프론트엔드 변경사항이 반영되지 않는 경우

```bash
npm run build
# 또는
php artisan filament:upgrade
```

## 라이센스

MIT License

## 지원

문의사항이 있으시면 개발팀에 연락해 주세요.

