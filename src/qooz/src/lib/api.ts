const API_BASE = process.env.NEXT_PUBLIC_API_URL || 'http://localhost:8080/qooz/api'

async function fetchAPI(endpoint: string, data?: Record<string, string>) {
  const url = new URL(`${API_BASE}/${endpoint}`)
  
  try {
    let response
    if (data) {
      const formData = new FormData()
      Object.entries(data).forEach(([key, value]) => {
        formData.append(key, value)
      })
      
      response = await fetch(url.toString(), {
        method: 'POST',
        body: formData,
      })
    } else {
      response = await fetch(url.toString())
    }
    
    const text = await response.text()
    if (!text.startsWith('{')) {
      console.error('Non-JSON response:', text.substring(0, 100))
      return { error: 'Invalid response', raw: text.substring(0, 100) }
    }
    
    return JSON.parse(text)
  } catch (err) {
    console.error('API fetch error:', err)
    return { error: 'Network error' }
  }
}

export const api = {
  // Auth
  auth: {
    login: (email: string, password: string) => 
      fetchAPI('auth/index.php', { action: 'login', email, password }),
    register: (email: string, password: string, nama: string) => 
      fetchAPI('auth/index.php', { action: 'register', email, password, nama }),
  },
  
  // Quiz
  quiz: {
    list: (userId: string) => 
      fetchAPI(`quiz/index.php?action=list&user_id=${userId}`),
    detail: (id: string) => 
      fetchAPI(`quiz/index.php?action=detail&id=${id}`),
    create: (userId: string, judul: string, deskripsi: string) => 
      fetchAPI('quiz/index.php', { action: 'create', user_id: userId, judul, deskripsi }),
    delete: (userId: string, quizId: string) => 
      fetchAPI('quiz/index.php', { action: 'delete', user_id: userId, quiz_id: quizId }),
    addQuestion: (userId: string, quizId: string, soal: string, opsi1: string, opsi2: string, opsi3: string, opsi4: string, jawaban: string, waktu: string) => 
      fetchAPI('quiz/index.php', { action: 'add_question', user_id: userId, quiz_id: quizId, soal, opsi_1: opsi1, opsi_2: opsi2, opsi_3: opsi3, opsi_4: opsi4, jawaban_benar: jawaban, waktu_detik: waktu }),
    updateQuestion: (userId: string, questionId: string, soal: string, opsi1: string, opsi2: string, opsi3: string, opsi4: string, jawaban: string, waktu: string) => 
      fetchAPI('quiz/index.php', { action: 'update_question', user_id: userId, question_id: questionId, soal, opsi_1: opsi1, opsi_2: opsi2, opsi_3: opsi3, opsi_4: opsi4, jawaban_benar: jawaban, waktu_detik: waktu }),
    deleteQuestion: (userId: string, quizId: string, questionId: string) => 
      fetchAPI('quiz/index.php', { action: 'delete_question', user_id: userId, quiz_id: quizId, question_id: questionId }),
  },
  
  // Game
  game: {
    create: (quizId: string, userId: string) => 
      fetchAPI('game/index.php', { action: 'create', quiz_id: quizId, user_id: userId }),
    state: (sessionId: string) => 
      fetchAPI(`game/index.php?action=state&session_id=${sessionId}`),
    byPin: (pin: string) => 
      fetchAPI(`game/index.php?action=by_pin&pin=${pin}`),
    start: (sessionId: string) => 
      fetchAPI('game/index.php', { action: 'start', session_id: sessionId }),
    next: (sessionId: string) => 
      fetchAPI('game/index.php', { action: 'next', session_id: sessionId }),
    endQuestion: (sessionId: string) => 
      fetchAPI('game/index.php', { action: 'end_question', session_id: sessionId }),
  },
  
  // Player
  player: {
    join: (pin: string, nama: string) => 
      fetchAPI('player/index.php', { action: 'join', pin, nama }),
    answer: (playerId: string, questionId: string, sessionId: string, jawaban: string, waktuMs: string) => 
      fetchAPI('player/index.php', { action: 'answer', player_id: playerId, question_id: questionId, session_id: sessionId, jawaban, waktu_ms: waktuMs }),
    score: (playerId: string) => 
      fetchAPI('player/index.php', { action: 'score', player_id: playerId }),
    state: (playerId: string) => 
      fetchAPI(`player/index.php?action=state&player_id=${playerId}`),
  },
}
