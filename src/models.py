import pickle

def save_model(model, vectorizer, filepath):
    """
    Save the trained model and vectorizer to a file.
    """
    with open(filepath, 'wb') as file:
        pickle.dump((model, vectorizer), file)

def load_model(filepath):
    """
    Load the model and vectorizer from a file.
    """
    import os

    if os.path.exists(filepath):
        with open(filepath, 'rb') as file:
            return pickle.load(file)
    else:
        raise FileNotFoundError(f"Model file {filepath} not found.")
