# 일본 지진 알림 마스토돈 코드
- 각 잡고 만드려다가 중간에 귀찮아서 ~~잘 돌아가면 그만이지 마인드로 대충~~ 만들었습니다.
- https://ani.work/@ko_KR_japan_earthquake_bot 에서 구동되는 일본 지진 알림 봇 소스코드

## 사용방법
- 파일 이름을 변경하고 토큰을 넣으세요.
  - `token.txt.example` -> `token.txt`

- 도커를 깔고 아래 커맨드를 실행하세요.
```bash
docker-compose up --build
```

잘 된다 싶으면 아래 커맨드로 마무리하세요.
```bash 
docker-compose up -d
```

## 자신의 서버로 바꾸고 싶은 경우
- `src/App/App.php` 의 파일에서 `https://ani.work` 를 찾아서 알아서 변경하세요.
