# src/data_preparation.py
import os
from src.handler import extract_queries_from_php

def load_php_files(directory, label):
    """
    Loads PHP files from the given directory, extracts queries, and assigns a label.
    Returns a list of extracted queries and corresponding labels.
    # """
    files = []
    labels = []
    for filename in os.listdir(directory):
        filepath = os.path.join(directory, filename)
        if os.path.isfile(filepath):
            with open(filepath, 'r') as file:
                content = file.read()
                queries = extract_queries_from_php(content, 'safe' if label == 0 else 'unsafe')

                if queries:  # Only add non-empty queries
                    for q in queries:
                        files.append(q)
                        labels.append(label)
    return files, labels

def prepare_dataset(clean_dir, unsafe_dir):
    """
    Prepares the dataset by loading both clean and unsafe queries.
    Returns two lists: queries and labels.
    """
    clean_queries, clean_labels = load_php_files(clean_dir, 0)  # 0 for safe queries
    unsafe_queries, unsafe_labels = load_php_files(unsafe_dir, 1)  # 1 for unsafe queries
    X = clean_queries + unsafe_queries
    y = clean_labels + unsafe_labels
    return X, y
