# src/utils.py
import pickle
from src import app  # Import the app object

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

def extract_queries_from_php(file_content):
    """
    Extracts lines of code that match:
    $<variable>->select(), $<variable>->from(), $<variable>->where(), $<variable>->get(),
    $<variable>->get_where(), $<variable>->query('CALL'), $<variable>->query('BEGIN')
    """
    import re

    pattern = re.compile(r'->query\(((\'|\")\w+(\'|\")|)\)', re.IGNORECASE)
    matches = pattern.findall(file_content)

    # Check for the presence of result-related methods
    result_methods = re.compile(r'^\$[\s\S]+->((select|from|where|get|get_where)\(((\'|\")\w+m(\'|\")|)\)|query\(((\'|\")(CALL|BEGIN)(\'|\")|)\))', re.IGNORECASE)
    result_matches = result_methods.findall(file_content)

    if matches:
        return "------".join(matches)  # Return extracted queries if found
    elif result_matches:
        return ""

    return ""

def check_php_file_for_query(filepath, vectorizer, model):
    """
    Given a PHP file path, checks whether it contains queries and if they are unsafe.
    """
    with open(filepath, 'r') as file:
        content = file.read()
        queries = extract_queries_from_php(content)

        if queries:
            queries_vect = vectorizer.transform([queries])
            prediction = model.predict(queries_vect)
            res_str = ''

            if prediction[0] == 1:
                res_str = f"[{filepath}]: {queries}"
                app.unsafe_logger.error(res_str)
            else:
                res_str = f"No unsafe queries: [{filepath}]"
                app.general_logger.info(res_str)

            return res_str
        else:
            res_str = f"No query found: [{filepath}]"
            app.general_logger.info(res_str)
            return res_str
