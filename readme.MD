# Projek Iseng

Machine Learning untuk cek cara penulisan query di CodeIgniter 3 apakah aman atau tidak. Cuma percobaan saja untuk belajar Machine Learning menggunakan `scikit-learn`.

## Cara setup:
- Buat `virtualenv`
```
virtualenv venv
```
- Jalankan `virtualenv`
```
.\venv\Scripts\activate
```
- Install dependencies
```
pip install -r requirements.txt
```

## Cara pakai:
### Untuk training model
- Copas model/file dengan query yang aman ke folder `data/clean_queries`
- Copas model/file dengan query yang tidak aman ke folder `data/unsafe_queries`
- Jalankan perintah ini di dalam `virtualenv`
```
python train.py
```

### Untuk running program
- Buka file `run.py`
- Edit list absolute path di variable `PATHS`
- Jalankan perintah ini di dalam `virtualenv`
```
python run.py
```