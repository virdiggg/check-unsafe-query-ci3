# src/train_model.py
from sklearn.feature_extraction.text import CountVectorizer
from sklearn.model_selection import train_test_split
from sklearn.ensemble import RandomForestClassifier
from sklearn.metrics import accuracy_score
from src.utils import save_model
from src.data_preparation import prepare_dataset
import os

def train_model():
    clean_dir = os.path.join('data', 'clean_queries')
    unsafe_dir = os.path.join('data', 'unsafe_queries')

    X, y = prepare_dataset(clean_dir, unsafe_dir)

    vectorizer = CountVectorizer()
    X_vect = vectorizer.fit_transform(X)

    X_train, X_test, y_train, y_test = train_test_split(X_vect, y, test_size=0.2, random_state=42)

    clf = RandomForestClassifier(n_estimators=100, random_state=42)
    clf.fit(X_train, y_train)

    y_pred = clf.predict(X_test)
    print(f"Accuracy: {accuracy_score(y_test, y_pred)}")

    # Save the model and vectorizer
    save_model(clf, vectorizer, os.path.join('models', 'query_model.pkl'))

if __name__ == "__main__":
    train_model()
