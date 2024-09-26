from src import app  # Import the app object

def extract_queries_from_php(file_content, model='safe'):
    """
    Extracts lines of code that match:
    $<variable>->select(), $<variable>->from(), $<variable>->where(), $<variable>->get(),
    $<variable>->get_where(), $<variable>->query('CALL'), $<variable>->query('BEGIN')
    """
    import re

    if (model == 'safe'):
        pattern = re.compile(r'->((?:select|from|where|or_where|like|or_like|get|get_where|group_start|group_end|group_by|join|count_all_results|num_rows)\([^)]*\)|query\(((\'|\")(CALL|BEGIN)(\'|\")|)\))', re.IGNORECASE)
    else:
        pattern = re.compile(r'->query\([^)]*\)', re.IGNORECASE)

    matches = pattern.findall(file_content)

    arr_matches = []
    for match in matches:
        if match:
            if isinstance(match, str):
                arr_matches.append(match)
            else:
                # match is a tuple, so we need to unpack it
                for m in match:
                    # can be empty string
                    if m:
                        arr_matches.append(m)

    if arr_matches:
        return arr_matches

    return ""

def check_php_file_for_query(filepath, vectorizer, model):
    """
    Given a PHP file path, checks whether it contains queries and if they are unsafe.
    """
    with open(filepath, 'r') as file:
        content = file.read()
        queries = extract_queries_from_php(content)

        if queries:
            for q in queries:
                queries_vect = vectorizer.transform([q])
                prediction = model.predict(queries_vect)
                res_str = ''

                if prediction[0] == 1:
                    res_str = f"[{filepath}]: {q}"
                    app.unsafe_logger.error(res_str)
                else:
                    res_str = f"No unsafe queries: [{filepath}]"
                    app.general_logger.info(res_str)

                return res_str
        else:
            res_str = f"No query found: [{filepath}]"
            app.general_logger.info(res_str)
            return res_str
