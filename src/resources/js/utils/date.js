export function formatDateTime(dateString) {
  const date = new Date(dateString);
  if (isNaN(date)) return '';
  
  return new Intl.DateTimeFormat('pt-BR', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
  }).format(date);
}

export function formatToDateInput(dateString) {
  const date = new Date(dateString);
  if (isNaN(date)) return '';

  const year = date.getFullYear();
  const month = String(date.getMonth() + 1).padStart(2, '0');
  const day = String(date.getDate()).padStart(2, '0');

  return `${year}-${month}-${day}`;
}