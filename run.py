# <root>/run.py
from src.handler import check_php_file_for_query
from src.models import load_model
from src import app
import os
from datetime import datetime

# Load the trained model and vectorizer
model_path = os.path.join('models', 'query_model.pkl')
clf, vectorizer = load_model(model_path)

# Set your path here
PATHS = [
    r"D:\laragon\www\approval\application\models",
    r"D:\laragon\www\mitra_less\application\controllers",
    r"D:\laragon\www\mitra_less\application\models"
]

for index, p in enumerate(PATHS):
    if not os.path.isdir(p):
        app.general_logger.info(f"Directory {p} not found.\n\n")
    else:
        ind = "\n"
        if index == 0:
            ind = ""
        app.general_logger.info(f"{ind}Running on directory {p}@{datetime.now()}.\n============================================================================================\n\n\n\n")

        # File to check
        for root, dirs, files in os.walk(p):
            for file_name in files:
                # Only process PHP files
                if file_name.endswith(".php"):
                    file_path = os.path.join(root, file_name)
                    # Check the PHP file
                    result = check_php_file_for_query(file_path, vectorizer, clf)


